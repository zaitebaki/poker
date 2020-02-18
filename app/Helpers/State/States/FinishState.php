<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class FinishState extends State
{

    public function __construct($context)
    {
        parent::__construct($context);

        $this->context->userCards         = $this->context->extractUserCardsFromRedis();
        $this->context->opponentUserCards = $this->context->extractOpponentUserCardsFromRedis();

        // получить итоговые комбинации и очки
        if (!$this->isResultsAlreadyAnnounced()) {
            $gameBones = Cards::getCombintationsAndPoits($this->context->userCards, $this->context->opponentUserCards);
            $this->summarizeGameResults($gameBones);

            // сохранить состояние кнопки "продолжить" (кнопка недоступна)
            $this->context->saveNewGameButtonIndicator($this->context->currentUser->id);
        }

        $this->context->userCombination     = $this->getUserCombinationFromRedis();
        $this->context->opponentCombination = $this->getOpponentCombinationFromRedis();
        $this->context->userPoints          = $this->getUserPointsFromRedis();
        $this->context->opponentPoints      = $this->getOpponentPointsFromRedis();

        $this->context->money        = $this->context->extractMoney();
        $this->context->bankMessages = $this->context->extractBankMessages();

        // вычислить результат партии,
        // если игра закончилась дропом карт одним из игроков
        $statusGame = $this->getEndStatusGame();
        $money      = 0;
        if ($statusGame === 'drop' || $statusGame === 'winDrop') {
            if ($statusGame === "drop") {
                $money                     = $this->getDropMoney();
                $this->context->statusText =
                __(
                    'main_page_content.gamePage.statusMessages.gameOverMessage2',
                    ['money' => $money]
                );
                $this->context->isVictory = -1;
            } elseif ($statusGame === "winDrop") {
                $money                     = $this->getDropMoney();
                $this->context->statusText =
                __(
                    'main_page_content.gamePage.statusMessages.gameOverMessage1',
                    ['user' => $this->context->opponentUser->name, 'money' => $money]
                );
                $this->context->isVictory = 1;
            }
        // вычислить результат игры,
        // если необходимо рассчитать комбинацию и очки
        } else {
            $winnerId = $this->context->getWinnerIdFromRedis();

            // победа
            $money = $this->context->money / 2;
            if ($winnerId === (string) $this->context->currentUser->id) {
                if ($statusGame === 'check') {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.checkWinFinishMessage',
                        ['user' => $this->context->opponentUser->name, 'money' => $money]
                    );
                } elseif ($statusGame === 'equal') {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.equalWinFinishMessage',
                        ['user' => $this->context->opponentUser->name, 'money' => $money]
                    );
                } else {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.winFinishMessage',
                        ['money' => $money]
                    );
                }
                $this->context->isVictory = 1;

            // проигрыш
            } elseif ($winnerId === (string) $this->context->opponentUser->id) {
                if ($statusGame === 'check') {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.checkLoseFinishMessage',
                        ['user' => $this->context->opponentUser->name, 'money' => $money]
                    );
                } elseif ($statusGame === 'equal') {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.equalLoseFinishMessage',
                        ['user' => $this->context->opponentUser->name, 'money' => $money]
                    );
                } else {
                    $this->context->statusText =
                    __(
                        'main_page_content.gamePage.statusMessages.loseFinishMessage',
                        ['money' => $money]
                    );
                }
                $this->context->isVictory = -1;

                // ничья
            } elseif ($winnerId === '0') {
                $this->context->statusText =
                __(
                    'main_page_content.gamePage.statusMessages.drawFinishMessage',
                    ['money' => $money]
                );
                $this->context->isVictory = 0;
            }
        }

        $this->context->buttons = ['then'];

        // обновить баланс и статистику пользователя в базе данных
        $this->updateUserBalanceAndPayment($money);
    }

     /**
     * Инициализировать действие -
     * следующая партия
     */
    public static function then($user, $roomName)
    {
        $userId =  $user->id;
        $idUserCurrent  = (int) Redis::get($roomName . ':idUserCurrent');
        $idUserOpponent = (int) Redis::get($roomName . ':idUserOpponent');

        if ($userId === $idUserCurrent) {
            $role = 'currentUser';
            $anotherUserId = $idUserOpponent;
        } else {
            $role = 'opponentUser';
            $anotherUserId = $idUserCurrent;
        }

        $opponentState = self::getOpponentState($roomName, $anotherUserId);

        $pieces = explode("_", $roomName);
        $roomId = $pieces[1];

        if ($opponentState === 'WaitingState') {
            $winnerId = (int)self::getWinner($roomName);
            self::removeUserDataFromRedis($roomName, $userId);
            self::removeUserDataFromRedis($roomName, $anotherUserId);
            self::removeCommonDataFromRedis($roomName);
            
            if ($winnerId === $userId && $role === 'currentUser') {
                self::setIdUserCurrent($roomName, $idUserOpponent);
                self::setIdUserOpponent($roomName, $idUserCurrent);
                self::saveWhoStartRound($roomName, 'winner');
            }
            if ($winnerId === $userId && $role === 'opponentUser') {
                self::setIdUserCurrent($roomName, $idUserCurrent);
                self::setIdUserOpponent($roomName, $idUserOpponent);
                self::saveWhoStartRound($roomName, 'winner');
            }
            if ($winnerId !== $userId && $role === 'opponentUser') {
                self::setIdUserCurrent($roomName, $idUserOpponent);
                self::setIdUserOpponent($roomName, $idUserCurrent);
                self::saveWhoStartRound($roomName, 'looser');
            }
            if ($winnerId !== $userId && $role === 'currentUser') {
                self::setIdUserCurrent($roomName, $idUserCurrent);
                self::setIdUserOpponent($roomName, $idUserOpponent);
                self::saveWhoStartRound($roomName, 'looser');
            }
            self::setState($roomName, $userId, 'ReadyState');
        } else {
            self::setState($roomName, $userId, 'NewRoundState');
        }
    }

    /**
     * Проверить рассчитаны ли результаты игры
     */
    private function isResultsAlreadyAnnounced(): bool
    {
        return Redis::exists($this->context->roomName . ":winner");
    }

    /**
     * Определить победителя и сохранить результаты
     */
    private function summarizeGameResults(array $gameBones): void
    {
        // определить победителя
        $currenUserPoints   = $gameBones['points']['currentUserPoints'];
        $opponentUserPoints = $gameBones['points']['opponentUserPoints'];

        if ($currenUserPoints === $opponentUserPoints) {
            $this->context->saveWinner('0');
        } elseif ($currenUserPoints > $opponentUserPoints) {
            $this->context->saveWinner($this->context->currentUser->id);
        } elseif ($currenUserPoints < $opponentUserPoints) {
            $this->context->saveWinner($this->context->opponentUser->id);
        }

        $this->context->saveUserPoints($currenUserPoints, $this->context->currentUser->id);
        $this->context->saveUserPoints($opponentUserPoints, $this->context->opponentUser->id);

        // сохранить комбинации игроков
        $currentUserCombination  = $gameBones['combinations']['currentUserCombination'];
        $opponentUserCombination = $gameBones['combinations']['opponentUserCombination'];

        $this->context->saveUserCombination($currentUserCombination, $this->context->currentUser->id);
        $this->context->saveUserCombination($opponentUserCombination, $this->context->opponentUser->id);
    }

    /**
     * Извлечь комбинацию текущего пользователя
     */
    private function getUserCombinationFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":combination");
    }

    /**
     * Сохранить значение поля
     * кто начал раунд? - winner|looser
     */
    private static function saveWhoStartRound($roomName, $value)
    {
        Redis::set($roomName . ':whoStartRound', $value);
    }

    /**
     * Извлечь комбинацию пользователя-оппонента
     */
    private function getOpponentCombinationFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->opponentUser->id . ":combination");
    }

    /**
     * Извлечь очки текущего пользователя
     */
    private function getUserPointsFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":points");
    }

    /**
     * Извлечь очки пользователя-оппонента
     */
    private function getOpponentPointsFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->opponentUser->id . ":points");
    }

    /**
     * Извлечь статус окончания игры при дропе
     */
    public function getEndStatusGame()
    {
        $isBool = Redis::exists($this->context->roomName . ':' . $this->context->currentUser->id . ":endGameStatus");
        if ($isBool === 0) {
            return false;
        }

        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":endGameStatus");
    }

    /**
     * Извлечь количество проигранных/выигранных денег при дропе
     */
    public function getDropMoney(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":dropGameMoney");
    }

    /**
     * Обновить баланс пользователя в базе данных
     */
    private function updateUserBalanceAndPayment($money)
    {
        // проверить существование победителя
        // если победителя нет - результаты партии
        // уже сохранены в БД
        if (!$this->isStartGameStatus()) {
            return;
        }

        // удалить информацию о победителе
        $this->deleteStartGameStatus();

        $user    = $this->context->currentUser;
        $victory = $this->context->isVictory;

        if ($victory === 1) {
            // $user->victory = $user->victory + 1;
            $user->increment('victory', 1);
        }
        if ($victory === -1) {
            // $user->gameover = $user->gameover + 1;
            $user->increment('gameover', 1);
        }

        $money = $this->context->isVictory * $money;
        $user->increment('balance', $money);

        // обновить payment с пользователем-оппонентом
        $user->savePaymentValue($this->context->opponentUser->id, $money);
    }

    /**
     * Извлечь флаг существования информации о победителе
     */
    public function isStartGameStatus()
    {
        return Redis::exists($this->context->roomName . ':' . $this->context->currentUser->id . ":startGameStatus");
    }

    /**
     * Извлечь флаг существования информации о победителе
     */
    public function deleteStartGameStatus()
    {
        Redis::del($this->context->roomName . ':' . $this->context->currentUser->id . ":startGameStatus");
    }

    /**
     * Удалить данные пользователя из БД Redis
     */
    public static function removeUserDataFromRedis($roomName, $currentUserId)
    {
        $namesUserKeys = [
            'userCards',
            'pushStartBet',
            'correctionMessage',
            'combination',
            'points',
            'endGameStatus',
            'dropGameMoney',
            'isAlreadyChangedCards',
        ];

        $begKeyString = $roomName . ':' . $currentUserId . ':';
        Redis::pipeline(function ($pipe) use ($namesUserKeys, $begKeyString) {
            foreach ($namesUserKeys as $key => $value) {
                $pipe->del($begKeyString . $value);
            }
        });
    }

    /**
     * Удалить общие данные из БД Redis
     */
    public static function removeCommonDataFromRedis($roomName)
    {
        $namesCommonKeys = [
            'cards',
            'money',
            'messages',
            'winner',
            'addOpponentMoney',
            'countFirstUserChangeCards',
            'increaseAfterEqualMoney',
            'opponentStatusCheck',
        ];

        $begKeyString = $roomName . ':';
        Redis::pipeline(function ($pipe) use ($namesCommonKeys, $begKeyString) {
            foreach ($namesCommonKeys as $key => $value) {
                $pipe->del($begKeyString . $value);
            }
        });
    }

    /**
     * Удалить оставшиеся данные из БД Redis
     * при завершениии сеанса игры
     */
    public static function removeAllRoomDataFromRedis($roomName, $userId, $opponentUserId)
    {
        $namesCommonKeys = [
            'idUserCurrent',
            'idUserOpponent',
            'isStartGameFlag',
            $userId . ':startGameStatus',
            $opponentUserId . ':startGameStatus',
            $userId . ':startButtonIndicator',
            $opponentUserId . ':startButtonIndicator',
            $userId . ':WaitingState',
            $opponentUserId . ':WaitingState',
            $userId . ':state',
            $opponentUserId . ':state',
            (string)$userId,
            (string)$opponentUserId
        ];

        $begKeyString = $roomName . ':';
        Redis::pipeline(function ($pipe) use ($namesCommonKeys, $begKeyString) {
            foreach ($namesCommonKeys as $key => $value) {
                $pipe->del($begKeyString . $value);
            }
        });

        $key = 'room:' . $userId . ':' . $opponentUserId;
        Redis::del($key);
        $key = 'room:' . $opponentUserId . ':' . $userId;
        Redis::del($key);
    }

    /**
     * Установить состояние пользователя в ReadyState
     */
    private static function setState($roomName, $userId, $nameState)
    {
        Redis::set($roomName . ':' . $userId . ':state', $nameState);
    }

    /**
     *Извлечь id текущего пользователя
     */
    private static function getCurrentUserId($roomName)
    {
        return Redis::get($roomName . ':idUserCurrent');
    }

    /**
     *Извлечь id пользователя-оппонента
     */
    private static function getOpponentUserId($roomName)
    {
        return Redis::get($roomName . ':idUserOpponent');
    }

    /**
     * Сохранить id победителя в Redis,
     * Сохранить 0 в случае ничьи
     */
    private static function getWinner(string $roomName)
    {
        return Redis::get($roomName . ":winner");
    }

    /**
     * Установить значение idUserCurrent
     */
    private static function setIdUserCurrent($roomName, $userId)
    {
        Redis::set($roomName . ':idUserCurrent', $userId);
    }

    /**
     * Установить значение idUserOpponent
     */
    private static function setIdUserOpponent($roomName, $userId)
    {
        Redis::set($roomName . ':idUserOpponent', $userId);
    }

    /**
     * Извлечь текущее состояние оппонента
     */
    private static function getOpponentState($roomName, $opponentUserId): string
    {
        return Redis::get($roomName . ':' . $opponentUserId . ':state');
    }

    /**
     * Удалить состояние кнопки "продолжить"
     * состояние - ДОСТУПНА
     */
    public function delNewGameButtonIndicator($roomId, $userId)
    {
        Redis::del($this->context->roomName . ':' .  $userId . ':newGameButtonIndicator');
        \App\Events\SendUpdateIndicatorButtonStatus::dispatch($roomId);
    }
}

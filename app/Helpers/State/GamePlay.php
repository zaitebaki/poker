<?php

namespace App\Helpers\State;

use App\User;
use Illuminate\Support\Facades\Redis;

/**
 * Паттерн Состояние
 *
 * Назначение: Позволяет объектам менять поведение в зависимости от своего
 * состояния. Извне создаётся впечатление, что изменился класс объекта.
 */

/**
 * Контекст определяет интерфейс, представляющий интерес для клиентов. Он также
 * хранит ссылку на экземпляр подкласса Состояния, который отображает текущее
 * состояние Контекста.
 */

// room_x:count
// room_x:1:state - имя состояния для текущего пользователя
// room_x:idUserCurrent
// room_x:idUserOpponent
// room_x:1:WaitingState - аргументы для конструктора состояния
// room_x:1:userCards
// room_x:cards
// room_x:money
// room_x:1:pushStartBet - пользователь сделал начальную ставку
// room_x:messages
// room_x:1:correctionMessage
// room_x:countFirstUserChangeCards
// room_x:addOpponentMoney
// room_x:increaseAfterEqualMoney
// room_x:winner
// room_x:1:combination
// room_x:1:points
// room_x:1:endGameStatus - игра закончилась дропом
// room_x:1:dropGameMoney
// room_x:1:isAlreadyChangedCards
    // room_x:startGameStatus
// room_x:opponentStatusCheck
// room_x:startButtonIndicator
// room_x:newGameButtonIndicator

class GamePlay
{
    /**
     * @var State Ссылка на текущее состояние Контекста.
     */
    const START_BET = 5;

    public $state;
    public $statusText;
    public $currentUser;
    public $opponentUser;
    public $buttons;
    public $roomName;
    public $roomId;
    public $userCards;
    public $role;
    public $request;
    public $countFirstUserChangeCards;
    public $indicator               = 'ready';
    public $money                   = 0;
    public $bankMessages            = [];
    public $addOpponentMoney        = '';
    public $increaseAfterEqualMoney = '';
    public $opponentUserCards;
    public $userCombination;
    public $opponentCombination;
    public $userPoints;
    public $opponentPoints;

    /**
     * @var int $isVictory  Коде результата игры
     * -1 - проигрыш
     * 0 - ничья
     * 1 - победа
     */
    public $isVictory;

    public $dump = '';

    public function __construct($user, string $roomName, $request)
    {
        $this->roomName = $roomName;
        $this->request  = $request;

        $this->roomId = $this->getRoomId();

        // определить создателя и приглашенного игрока
        $idUserCurrent  = Redis::get($roomName . ':idUserCurrent');
        $idUserOpponent = Redis::get($roomName . ':idUserOpponent');

        if ($user->id === (int) $idUserCurrent) {
            $this->currentUser  = $user;
            $this->opponentUser = User::find($idUserOpponent);
            $this->role         = 'currentUser';
        } else {
            $this->currentUser  = $user;
            $this->opponentUser = User::find($idUserCurrent);
            $this->role         = 'opponentUser';
        }

        // инициализировать состояние из Redis-хранилища
        $stateName            = Redis::get($roomName . ':' . $this->currentUser->id . ':state');
        $argumentsStorageName = $roomName . ':' . $this->currentUser->id . ':' . $stateName;
        $stateArguments       = Redis::lrange($argumentsStorageName, 0, 6);

        $stateArguments = array_reverse($stateArguments);
        $stateName      = 'App\\Helpers\\State\\States\\' . $stateName;

        $this->state = new $stateName($this, ...$stateArguments);
    }

    public function updateState(string $nameState, ...$arg): void
    {
        $stateName            = 'App\\Helpers\\State\\States\\' . $nameState;
        $this->state          = new $stateName($this, ...$arg);
        $argumentsStorageName = $this->roomName . ':' . $this->currentUser->id . ':' . $nameState;
        Redis::del($argumentsStorageName);

        if (!empty($arg)) {
            Redis::lpush($argumentsStorageName, ...$arg);
        }
        Redis::set($this->roomName . ':' . $this->currentUser->id . ':state', $nameState);
    }

    public function getGameParameters(): array
    {
        return array(
            'roomId' => $this->roomId,
            'statusMessage'           => $this->statusText,
            'buttons'                 => $this->buttons,
            'userCards'               => $this->userCards,
            'indicator'               => $this->indicator,
            'money'                   => (string) $this->money,
            'bankMessages'            => $this->bankMessages,
            'addOpponentMoney'        => (string) $this->addOpponentMoney,
            'increaseAfterEqualMoney' => (string) $this->increaseAfterEqualMoney,
            'isAlreadyChangedCards' => $this->getAlreadyChangedCardsStatus(),
            'opponentStatusCheck' => $this->getOpponentStatusCheck(),
            'startButtonIndicator' => $this->getStartButtonIndicator(),
            'dump' => $this->dump
        );
    }

    public function getFinishGameParameters(): array
    {
        return array(
            'roomId' => $this->roomId,
            'statusMessage'       => $this->statusText,
            'userCards'           => $this->userCards,
            'opponentUserCards'   => $this->opponentUserCards,
            'buttons'             => $this->buttons,
            'userCombination'     => $this->userCombination,
            'opponentCombination' => $this->opponentCombination,
            'isVictory'           => $this->isVictory,
            'money'               => (string) $this->money,
            'bankMessages'        => $this->bankMessages,
            'userPoints' => $this->userPoints,
            'opponentPoints' => $this->opponentPoints,
            'newGameButtonIndicator' => $this->getNewGameButtonIndicator(),
            'isAlreadyChangedCards' => $this->getAlreadyChangedCardsStatus(),
        );
    }

    /**
     * Получить id комнаты
     */
    private function getRoomId()
    {
        $pieces = explode("_", $this->roomName);
        return $pieces[1];
    }

    public function connectionCurrentUser(): void
    {
        $this->state->connectionCurrentUser();
    }

    public function connectionOpponentUser(): void
    {
        $this->state->connectionOpponentUser();
    }

    public function startGame()
    {
        return $this->state->startGame();
    }

    public function startChangeCards()
    {
        return $this->state->startChangeCards();
    }

    // public function startChangeCardsEvent()
    // {
    //     return $this->state->startChangeCardsEvent();
    // }

    public function changeCards()
    {
        return $this->state->changeCards();
    }

    public function addMoney()
    {
        return $this->state->addMoney();
    }

    public function check()
    {
        return $this->state->check();
    }

    public function opponentCheck()
    {
        return $this->state->opponentCheck();
    }

    public function equalAndAdd()
    {
        return $this->state->equalAndAdd();
    }

    public function equal()
    {
        return $this->state->equal();
    }

    public function gameOver()
    {
        return $this->state->gameOver();
    }

    public function then()
    {
        return $this->state->then();
    }

    public function delNewGameButtonIndicator()
    {
        return $this->state->delNewGameButtonIndicator($this->roomId, $this->opponentUser->id);
    }

    // сервисные функции
    public function setStatusText($text): void
    {
        $this->statusText = $text;
    }

    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * Отправить приглашение пользователю чтобы начать игру
     */
    public function dispatchInvitation(): void
    {
        // $this->opponentUser->invitations()->attach($this->currentUser->id);
        \App\Events\SendInvitation::dispatch($this->currentUser->id, $this->opponentUser->id);
    }

    public function getKeyStorageForCards(): string
    {
        return $this->roomName . ':cards';
    }

    public function saveUserCards()
    {
        $data = implode(",", $this->userCards);
        Redis::set($this->roomName . ':' . $this->currentUser->id . ':userCards', $data);
    }

    public function extractUserCardsFromRedis(): array
    {
        $data = Redis::get($this->roomName . ':' . $this->currentUser->id . ':userCards');
        return explode(',', $data);
    }

    public function extractOpponentUserCardsFromRedis(): array
    {
        $data = Redis::get($this->roomName . ':' . $this->opponentUser->id . ':userCards');
        return explode(',', $data);
    }

    public function getOpponentState()
    {
        return Redis::get($this->roomName . ':' . $this->opponentUser->id . ':state');
    }

    public function saveCountFirstUserChangeCards($cntCards): string
    {
        return Redis::set($this->roomName . ':countFirstUserChangeCards', $cntCards);
    }

    public function getCountFirstUserChangeCards(): string
    {
        return Redis::get($this->roomName . ':countFirstUserChangeCards');
    }

    public function pushStartingBet($moneySum)
    {
        $this->money        = $this->extractMoney();
        $this->bankMessages = $this->extractBankMessages();

        if (!$this->startBetsAlreadyPush()) {
            $this->money += $moneySum;
            $this->saveStartBetForUser();
        }
        $this->saveMoney();
    }

    private function startBetsAlreadyPush(): bool
    {
        return Redis::exists($this->roomName . ':' . $this->currentUser->id . ":pushStartBet");
    }

    public function extractMoney()
    {
        return Redis::get($this->roomName . ":money");
    }

    public function extractBankMessages()
    {
        $messages = Redis::lrange($this->roomName . ":messages", 0, 5);
        return array_reverse($messages);
    }

    public function saveMoney()
    {
        Redis::set($this->roomName . ":money", $this->money);
    }

    private function saveStartBetForUser()
    {
        Redis::set($this->roomName . ':' . $this->currentUser->id . ":pushStartBet", 'ok');
        $this->saveBankMessage('5');
    }

    public function saveBankMessage($money)
    {
        $data = $this->currentUser->login . '|' . $money;
        Redis::lpush($this->roomName . ':messages', $data);
    }

    public function getIncreaseAfterEqualMoney()
    {
        $moneyIncrease = $this->request->moneyIncrease;

        if (isset($moneyIncrease)) {
            $this->saveIncreaseAfterEqualMoney($moneyIncrease);
            return $moneyIncrease;
        } else {
            return $this->extractIncreaseAfterEqualMoney();
        }
    }

    public function saveIncreaseAfterEqualMoney($money)
    {
        Redis::set($this->roomName . ":increaseAfterEqualMoney", $money);
    }

    public function extractIncreaseAfterEqualMoney()
    {
        return Redis::get($this->roomName . ":increaseAfterEqualMoney");
    }

    /**
     * Сохранить id победителя в Redis,
     * Сохранить 0 в случае ничьи
     */
    public function saveWinner(int $idUser): void
    {
        Redis::set($this->roomName . ":winner", $idUser);
    }

    /**
     * Извлечь id победителя
     */
    public function getWinnerIdFromRedis()
    {
        return Redis::get($this->roomName . ':winner');
    }

    /**
     * Сохранить очки игрока
     */
    public function saveUserPoints(string $points, string $userId): void
    {
        Redis::set($this->roomName . ':' . $userId . ":points", $points);
    }

    /**
     * Сохранить комбинацию игрока
     */
    public function saveUserCombination(string $combination, string $userId): void
    {
        Redis::set($this->roomName . ':' . $userId . ":combination", $combination);
    }

    /**
     * Получить значение флага -
     * произошел ли обмен картами
     */
    private function getAlreadyChangedCardsStatus()
    {
        $flag = Redis::exists($this->roomName . ':' . $this->currentUser->id . ":isAlreadyChangedCards");
        if ($flag === 0) {
            return false;
        }
        return true;
    }

    /**
     * Получить значение флага -
     * доступна ли кнопка "Продолжить"
     */
    private function getNewGameButtonIndicator()
    {
        $flag = Redis::exists($this->roomName . ':' . $this->currentUser->id . ":newGameButtonIndicator");
        if ($flag === 0) {
            return false;
        }
        return true;
    }

    /**
     * Получить значение флага -
     * доступна ли кнопка "Начать игру"
     */
    private function getStartButtonIndicator()
    {
        $flag = Redis::exists($this->roomName . ':' . $this->currentUser->id . ":startButtonIndicator");
        if ($flag === 0) {
            return false;
        }
        return true;
    }

    /**
     * Получить значение флага -
     * был ли ход "чек"
     */
    private function getOpponentStatusCheck()
    {
        $flag = Redis::exists($this->roomName . ":opponentStatusCheck");
        if ($flag === 0) {
            return false;
        }
        return true;
    }

    /**
     * Сохранить флаг - пользователь поменял карты
     */
    public function saveChangedCardsFlag()
    {
        Redis::set($this->roomName . ':' . $this->currentUser->id . ":isAlreadyChangedCards", 'ok');
    }

    /**
     * Сохранить состояние кнопки "продолжить"
     * состояние - НЕДОСТУПНА
     */
    public function saveNewGameButtonIndicator($userId)
    {
        Redis::set($this->roomName . ':' .  $userId . ':newGameButtonIndicator', 'ok');
    }
}

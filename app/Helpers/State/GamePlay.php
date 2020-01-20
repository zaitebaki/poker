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

// room_1:count
// room_1:1:state - имя состояния для текущего пользователя
// room_1:idUserCurrent
// room_1:idUserOpponent
// room_1:1:WaitingState - аргументы для конструктора состояния
// room_1:1:userCards
// room_1:cards
// room_1:money
// room_1:1:pushStartBet - пользователь сделал начальную ставку
// room_1:messages
// room_1:1:correctionMessage
// room_1:countFirstUserChangeCards
// room_1:addOpponentMoney
// room_1:increaseAfterEqualMoney
// room_1:winner
// room_1:1:combination
// room_1:1:points
// room_1:1:endGameStatus - игра закончилась дропом
// room_1:1:dropGameMoney
// room_1:1:isAlreadyChangedCards
// room_1:startGameStatus
// room_1:opponentStatusCheck

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
            'statusMessage'           => $this->statusText,
            'buttons'                 => $this->buttons,
            'userCards'               => $this->userCards,
            'indicator'               => $this->indicator,
            'money'                   => (string) $this->money,
            'bankMessages'            => $this->bankMessages,
            'addOpponentMoney'        => (string) $this->addOpponentMoney,
            'increaseAfterEqualMoney' => (string) $this->increaseAfterEqualMoney,
            'isAlreadyChangedCards' => $this->getAlreadyChangedCardsStatus(),
            'opponentStatusCheck' => $this->getOpponentStatusCheck()
            // 'dump'                    => $this->dump,
        );
    }

    public function getFinishGameParameters(): array
    {
        return array(
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
            'isAlreadyChangedCards' => $this->getAlreadyChangedCardsStatus(),
            // 'dump'                => $this->dump
        );
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

    // сервисные функции
    public function setStatusText($text): void
    {
        $this->statusText = $text;
    }

    public function getStatusText(): string
    {
        return $this->statusText;
    }

    public function dispatchInvitation(): void
    {
        $this->opponentUser->invitations()->attach($this->currentUser->id);
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
}

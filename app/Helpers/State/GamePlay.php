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
// room_1:1:addOpponentMoney
// room_1:1:increaseAfterEqualMoney

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
        $stateArguments       = Redis::lrange($argumentsStorageName, 0, 5);

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
            'dump'                    => $this->dump,
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

    public function getOpponentState(): string
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
        return Redis::lrange($this->roomName . ":messages", 0, 5);
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
}

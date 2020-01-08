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

class GamePlay
{
    /**
     * @var State Ссылка на текущее состояние Контекста.
     */
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
    public $indicator = 'ready';

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
            'statusMessage' => $this->statusText,
            'buttons'       => $this->buttons,
            'userCards'     => $this->userCards,
            'indicator'     => $this->indicator,
            'dump'          => $this->dump,
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
}

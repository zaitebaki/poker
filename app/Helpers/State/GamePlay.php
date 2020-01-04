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
    public $dump;
    public $cards;

    public function __construct($user, string $roomName, $request)
    {
        // определить создателя и приглашенного игрока
        $idUserCurrent  = Redis::get($roomName . ':idUserCurrent');
        $idUserOpponent = Redis::get($roomName . ':idUserOpponent');

        if ($user->id === (int) $idUserCurrent) {
            $this->currentUser  = $user;
            $this->opponentUser = User::find($idUserOpponent);
        } else {
            $this->currentUser  = User::find($idUserOpponent);
            $this->opponentUser = $user;
        }

        // инициализировать состояние из Redis-хранилища
        $stateName            = Redis::get($roomName . ':' . $this->currentUser->id . ':state');
        $argumentsStorageName = $roomName . ':' . $this->currentUser->id . ':' . $stateName;
        $stateArguments       = Redis::lrange($argumentsStorageName, 0, 5);

        $stateArguments = array_reverse($stateArguments);
        $stateName      = 'App\\Helpers\\State\\States\\' . $stateName;

        $this->state = new $stateName($this, ...$stateArguments);

        // $this->state = new ReadyState($this, ...$stateArguments);

        $this->dump = $this->state->startGame();

        $this->roomName = $roomName;
    }

    public function updateState(string $nameState, ...$arg): void
    {
        $stateName   = 'App\\Helpers\\State\\States\\' . $nameState;
        $this->state = new $stateName($this, ...$arg);

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
        // return $this->state->startGame();
        // return json_encode($this->state->startGame(), JSON_UNESCAPED_UNICODE);
        // return ($this->state);

        return $this->state->startGame();
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

}

<?php

namespace App\Helpers\State;

use App\Helpers\State\State;
use App\Helpers\State\States\InitState;

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
    public $request;
    public $srcUser;
    public $dstUser;

    public function __construct($srcUser, $dstUser, $request)
    {
        // $this->transitionTo($state);
        $this->state = new InitState($this);

        $this->request = $request;

        $this->srcUser = $srcUser;
        $this->dstUser = $dstUser;
    }

    public function changeState(State $state)
    {
        $this->state = state;
    }

    public function connectionSrcUser()
    {
        $this->state->connectionSrcUser();
    }

    public function connectionDstUser()
    {
        $this->state->connectionDstUser();
    }

    // сервисные функции
    public function setStatusText($text)
    {
        $this->statusText = $text;
    }

    public function getStatusText()
    {
        return $this->statusText;
    }

    public function dispatchInvitation()
    {
        $this->dstUser->invitations()->attach($this->srcUser->id);

        \App\Events\SendInvitation::dispatch($this->srcUser->id, $this->dstUser->id);
    }
    /**
     * Контекст позволяет изменять объект Состояния во время выполнения.
     */
    // public function transitionTo(State $state): void
    // {
    //     echo "Context: Transition to " . get_class($state) . ".\n";
    //     $this->state = $state;
    //     $this->state->setContext($this);
    // }

    /**
     * Контекст делегирует часть своего поведения текущему объекту Состояния.
     */
    // public function request1(): void
    // {
    //     $this->state->handle1();
    // }

    // public function request2(): void
    // {
    //     $this->state->handle2();
    // }
}

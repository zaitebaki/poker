<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class InitState extends State
{

    public function connectionCurrentUser()
    {
        $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage',
            ['user' => $this->context->opponentUser->name]);
        $this->context->dispatchInvitation();
        $this->context->updateState('WaitingState', $waitingMessage);

    }

    public function connectionOpponentUser()
    {
        \App\Events\SendReadyStatus::dispatch();
        $this->context->updateState('ReadyState');
    }

    public function waitingOpponentUser()
    {
    }

    public function startGame()
    {
    }

    public function changeCards()
    {
    }

    public function addMoney()
    {
    }

    public function check()
    {
    }

    public function equalAndAdd()
    {
    }

    public function equal()
    {
    }

    public function gameOver()
    {
    }
}

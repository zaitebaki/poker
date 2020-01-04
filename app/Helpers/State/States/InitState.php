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
        $this->context->updateState('WaitingState', $waitingMessage, 5);

    }

    public function connectionOpponentUser()
    {
        \App\Events\SendReadyStatus::dispatch();
        $this->context->updateState('ReadyState');
    }

    public function waitingOpponentUser()
    {}

    public function startGame()
    {}
}

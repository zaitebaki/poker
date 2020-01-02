<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;
use App\Helpers\State\States\WaitingState;

class InitState extends State
{

    public function connectionSrcUser()
    {
        $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage',
            ['user' => $this->context->dstUser->name]);

        $this->context->dispatchInvitation();
        $this->state = new WaitingState($this->context, $waitingMessage);
    }

    public function connectionDstUser()
    {
        $this->state = new ReadyState($this->context);
        \App\Events\SendReadyStatus::dispatch();
    }

    public function waitingDstUser()
    {}

    public function startGame()
    {}
}

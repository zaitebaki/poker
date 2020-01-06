<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class WaitingState extends State
{
    public function __construct($context, $watingText)
    {
        parent::__construct($context);

        $this->context->statusText = $watingText;
        $this->context->buttons    = null;

    }

    public function waitingopponentUser()
    {}

    public function connectionOpponentUser()
    {}

    public function connectionCurrentUser()
    {}

    public function startGame()
    {
        $this->context->updateState('ReadyState');
        $this->context->startGame();
    }
}

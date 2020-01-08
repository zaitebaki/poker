<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class WaitingState extends State
{
    public function __construct($context, $watingText, $buttons = 'none', $extractCards = false)
    {
        parent::__construct($context);

        $this->context->statusText = $watingText;
        $this->context->buttons    = explode(',', $buttons);

        if($extractCards) {
            $this->context->userCards = $this->context->extractUserCardsFromRedis();

        }
        $this->context->indicator  = 'wait';
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
    public function changeCards()
    {
    }
}

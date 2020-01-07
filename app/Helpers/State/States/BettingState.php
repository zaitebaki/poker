<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class BettingState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.bettingMessage');
        $this->context->buttons    = ['addMoney', 'noMoney'];
        $this->context->userCards = $this->context->extractUserCardsFromRedis();

    }

    public function waitingOpponentUser()
    {
    }

    public function connectionOpponentUser()
    {
    }

    public function connectionCurrentUser()
    {
    }

    public function startGame()
    {
    }

    public function changeCards()
    {
    }
}

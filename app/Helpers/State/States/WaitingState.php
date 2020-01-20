<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class WaitingState extends State
{
    public function __construct($context, $watingText, $buttons = 'none', $extractCards = false, $extractMoney = true)
    {
        parent::__construct($context);

        $this->context->statusText = $watingText;
        $this->context->buttons    = explode(',', $buttons);

        if ($extractMoney) {
            $this->context->money                   = $this->context->extractMoney();
            $this->context->bankMessages            = $this->context->extractBankMessages();
            $this->context->increaseAfterEqualMoney = $this->context->extractIncreaseAfterEqualMoney();
        }

        if ($extractCards) {
            $this->context->userCards = $this->context->extractUserCardsFromRedis();

        }

        $this->context->indicator = 'wait';
    }

    public function startGame()
    {
        $this->context->updateState('ReadyState');
        $this->context->startGame();
    }

    public function waitingopponentUser()
    {
    }

    public function connectionOpponentUser()
    {
    }

    public function connectionCurrentUser()
    {
    }

    public function changeCards()
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

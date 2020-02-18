<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class WaitingState extends State
{
    public function __construct($context, $waitingText, $buttons = 'none', $extractCards = false, $extractMoney = true)
    {
        parent::__construct($context);

        $this->context->statusText = $waitingText;
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

    /**
     * Начать партию
     */
    public function startGame()
    {
        $this->context->updateState('ReadyState');
        $this->context->startGame();
    }
}

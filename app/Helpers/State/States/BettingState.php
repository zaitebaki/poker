<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class BettingState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $message = $this->context->request->correctionStatusMessage;
        if ($message === 'changeFinished') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.bettingMessage1',
                ['user' => $this->context->opponentUser->name]);

        } elseif ($message === 'betFinished') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.bettingMessage2',
                ['user' => $this->context->opponentUser->name, 'money' => $this->context->request->money]);
        }
        $this->context->buttons      = ['addMoney', 'noMoney'];
        $this->context->money        = $this->context->extractMoney();
        $this->context->bankMessages = $this->context->extractBankMessages();
        $this->context->userCards    = $this->context->extractUserCardsFromRedis();
        $this->context->indicator    = 'ready';
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

    public function addMoney()
    {
        $money = $this->context->request->money;
        $this->context->money += $money;
        $this->context->saveMoney();
        $this->context->saveBankMessage($money);

        $waitingMessage = __('main_page_content.gamePage.statusMessages.addMoneyMessageCurrent',
            ['user' => $this->context->opponentUser->name,
                'money' => $money]);
        $buttons = 'equal,equalAndAdd,gameOver';

        $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        \App\Events\SendFinishBettingStatus::dispatch($money);
    }
}

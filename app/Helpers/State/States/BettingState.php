<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class BettingState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $message                                = $this->getCorrectionStatusMessage();
        $this->context->addOpponentMoney        = $this->getAddOpponentMoney();
        $this->context->increaseAfterEqualMoney = $this->context->getIncreaseAfterEqualMoney();

        if ($message === 'changeFinished') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.bettingMessage1',
                ['user' => $this->context->opponentUser->name]);
            $this->context->buttons = ['addMoney', 'noMoney'];

        } elseif ($message === 'betFinished') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.bettingMessage2',
                ['user' => $this->context->opponentUser->name, 'money' => $this->context->addOpponentMoney]);
            $this->context->buttons = ['equal', 'equalAndAdd', 'gameOver'];

        } elseif ($message === 'check') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.checkMessageOpponent',
                ['user' => $this->context->opponentUser->name]);
            $this->context->buttons = ['addMoney', 'noMoney'];
        } elseif ($message === 'equalAndAdd') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.equalAndAddMoney',
                ['user'  => $this->context->opponentUser->name,
                    'money1' => $this->context->addOpponentMoney,
                    'money2' => $this->context->increaseAfterEqualMoney]);
            $this->context->buttons = ['equal', 'equalAndAdd', 'gameOver'];
        } elseif ($message === 'equal') {
            $this->context->statusText = __('main_page_content.gamePage.statusMessages.equalMoney',
                ['user'  => $this->context->opponentUser->name,
                    'money1' => $this->context->addOpponentMoney]);
            $this->context->buttons = [];
        }

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
        $this->saveAddOpponetMoney($money);

        $waitingMessage = __('main_page_content.gamePage.statusMessages.addMoneyMessageCurrent',
            ['user' => $this->context->opponentUser->name,
                'money' => $money]);
        $buttons = 'equal,equalAndAdd,gameOver';

        $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        \App\Events\SendFinishBettingStatus::dispatch($money, '0');
    }

    public function check()
    {
        $waitingMessage = __('main_page_content.gamePage.statusMessages.checkMessage',
            ['user' => $this->context->opponentUser->name]);
        $buttons = 'addMoney, noMoney';

        $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        \App\Events\SendFinishBettingStatus::dispatch('0', '0');
    }

    public function equalAndAdd()
    {
        $moneyequal = $this->context->request->moneyequal;
        $moneyAdd   = $this->context->request->moneyAdd;

        $fullMoney = $moneyequal + $moneyAdd;

        $this->context->money += $fullMoney;
        $this->context->saveMoney();
        $this->context->saveBankMessage($fullMoney);
        $this->saveAddOpponetMoney($moneyequal);
        $this->context->saveIncreaseAfterEqualMoney($moneyAdd);

        $waitingMessage = __('main_page_content.gamePage.statusMessages.addMoneyMessageCurrent2',
            ['user'  => $this->context->opponentUser->name,
                'money1' => $moneyequal, 'money2' => $moneyAdd]);
        $buttons = 'equal,equalAndAdd,gameOver';

        $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        \App\Events\SendFinishBettingStatus::dispatch($moneyequal, $moneyAdd);
    }

    public function equal()
    {
        $money = $this->context->request->money;
        $this->context->money += $money;
        $this->context->saveMoney();
        $this->context->saveBankMessage($money);
        // $this->saveAddOpponetMoney($money);

        // $waitingMessage = __('main_page_content.gamePage.statusMessages.addMoneyMessageCurrent',
        //     ['user' => $this->context->opponentUser->name,
        //         'money' => $money]);
        // $buttons = 'equal,equalAndAdd,gameOver';

        $this->context->updateState('FinishState');
        \App\Events\SendFinishBettingStatus::dispatch($money, 'equal');

    }

    public function gameOver()
    {
    }

    private function getCorrectionStatusMessage()
    {
        $message = $this->context->request->correctionStatusMessage;

        if (isset($message)) {
            $this->saveCorrectionMessage($message);
            return $message;
        } else {
            return $this->extractCorrectionMessage();
        }
    }

    private function saveCorrectionMessage($message)
    {
        Redis::set($this->context->roomName . ':' . $this->context->currentUser->id . ":correctionMessage", $message);
    }

    private function extractCorrectionMessage()
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":correctionMessage");
    }

    private function getAddOpponentMoney()
    {
        $money = $this->context->request->money;

        if (isset($money)) {
            $this->saveAddOpponetMoney($money);
            return $money;
        } else {
            return $this->extractAddOpponentMoney();
        }
    }

    private function saveAddOpponetMoney($money)
    {
        Redis::set($this->context->roomName . ":addOpponentMoney", $money);
    }

    private function extractAddOpponentMoney()
    {
        return Redis::get($this->context->roomName . ":addOpponentMoney");
    }

}

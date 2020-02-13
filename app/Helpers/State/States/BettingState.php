<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
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
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, $money, '0');
    }

    public function check()
    {
        $waitingMessage = __('main_page_content.gamePage.statusMessages.checkMessage',
            ['user' => $this->context->opponentUser->name]);
        $buttons = 'addMoney, noMoney';

        $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);

        $this->saveOpponentStatusCheck();
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, '0', '0');
    }

    public function opponentCheck()
    {
        $this->context->updateState('FinishState');
        $this->saveUserEndGameStatus($this->context->currentUser->id, 'check');
        $this->saveUserEndGameStatus($this->context->opponentUser->id, 'check');
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, '0', 'opponentCheck');
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
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, $moneyequal, $moneyAdd);
    }

    public function equal()
    {
        $money = $this->context->request->money;
        $this->context->money += $money;
        $this->context->saveMoney();
        $this->context->saveBankMessage($money);
        $this->saveUserEndGameStatus($this->context->opponentUser->id, 'equal');
        $this->context->updateState('FinishState');
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, $money, 'equal');
    }

    public function gameOver()
    {
        $this->context->userCards         = $this->context->extractUserCardsFromRedis();
        $this->context->opponentUserCards = $this->context->extractOpponentUserCardsFromRedis();
        $loseMoney                        = $this->context->request->money;

        $gameBones = Cards::getCombintationsAndPoits($this->context->userCards, $this->context->opponentUserCards);
        $this->context->saveWinner($this->context->opponentUser->id);

        // определить победителя
        $currenUserPoints   = $gameBones['points']['currentUserPoints'];
        $opponentUserPoints = $gameBones['points']['opponentUserPoints'];

        $this->context->saveUserPoints($currenUserPoints, $this->context->currentUser->id);
        $this->context->saveUserPoints($opponentUserPoints, $this->context->opponentUser->id);

        // сохранить комбинации игроков
        $currentUserCombination  = $gameBones['combinations']['currentUserCombination'];
        $opponentUserCombination = $gameBones['combinations']['opponentUserCombination'];

        $this->context->saveUserCombination($currentUserCombination, $this->context->currentUser->id);
        $this->context->saveUserCombination($opponentUserCombination, $this->context->opponentUser->id);

        $this->saveUserEndGameStatus($this->context->currentUser->id, 'drop');
        $this->saveUserEndGameStatus($this->context->opponentUser->id, 'winDrop');
        $this->saveDropMoney($this->context->currentUser->id, $loseMoney);
        $this->saveDropMoney($this->context->opponentUser->id, $loseMoney);

        // сохранить состояние кнопки "продолжить" (кнопка недоступна)
        $this->context->saveNewGameButtonIndicator($this->context->currentUser->id);
        $this->context->updateState('FinishState');
        \App\Events\SendFinishBettingStatus::dispatch($this->context->roomId, $loseMoney, 'drop');
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

    /**
     * Извлечь деньги игрока-оппонента
     */
    private function extractAddOpponentMoney()
    {
        return Redis::get($this->context->roomName . ":addOpponentMoney");
    }

    /**
     * Сохранить статус игры в случае дропа
     */
    private function saveUserEndGameStatus(int $idUser, string $status)
    {
        Redis::set($this->context->roomName . ':' . $idUser . ":endGameStatus", $status);
    }

    /**
     * Сохранить деньги, которые были проиграны/выиграны при дропе
     */
    private function saveDropMoney(int $idUser, int $money)
    {
        Redis::set($this->context->roomName . ':' . $idUser . ":dropGameMoney", $money);
    }

    /**
     * Сохранить деньги, которые были проиграны/выиграны при дропе
     */
    private function saveOpponentStatusCheck()
    {
        Redis::set($this->context->roomName . ":opponentStatusCheck", 'ok');
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

    public function then()
    {
    }
}

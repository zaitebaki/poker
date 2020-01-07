<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;

class StartedGameState extends State
{
    private $cards;
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->buttons    = ['changeCards', 'notChange'];
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.startedMessage');
        $this->context->userCards  = $this->context->extractUserCardsFromRedis();

        $keyStorage  = $this->context->getKeyStorageForCards();
        $this->cards = new Cards($keyStorage);

    }

    public function waitingOpponentUser()
    {}

    public function connectionOpponentUser()
    {}

    public function connectionCurrentUser()
    {}

    public function startGame()
    {
    }

    public function changeCards()
    {
        // проверить - если 1-ый игрок не поменял карты
        // то перевести 2-го игрока в состояние ожидания
        if ($this->context->role === 'opponentUser' && $this->context->getOpponentState() !== 'BettingState') {
            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage3',
                ['user' => $this->context->opponentUser->name]);
            $this->context->updateState('WaitingState', $waitingMessage);
            return;
        }

        $indexes    = $this->context->request->cardsIndexForChange;
        $indexesArr = explode(",", $indexes);
        $cntIndexes = count($indexesArr);

        if ($this->context->role === 'currentUser') {
            $newCards = $this->cards->getCards(10, $cntIndexes);
            $this->context->saveCountFirstUserChangeCards($cntIndexes);
        }

        if ($this->context->role === 'opponentUser') {
            $countCards = (int) $this->context->getCountFirstUserChangeCards();
            $newCards   = $this->cards->getCards(10 + $countCards, $cntIndexes);
        }

        $this->context->dump = json_encode($indexesArr);

        for ($i = 0, $j = 0; $i < 5; $i++) {
            if ($j < $cntIndexes && $i == (int) $indexesArr[$j]) {
                $this->context->userCards[$i] = $newCards[$j];
                ++$j;
            }
        }
        $this->context->saveUserCards();
        $this->context->updateState('BettingState');
    }
}

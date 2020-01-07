<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class StartedGameState extends State
{
    private $cards;
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->buttons    = ['changeCards', 'notChange'];
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.startedMessage');
        $this->context->userCards  = $this->extractUserCardsFromRedis();

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
        $indexes    = $this->context->request->cardsIndexForChange;
        $indexesArr = explode(",", $indexes);
        $cntIndexes = count($indexesArr);

        $newCards = $this->cards->getCards(10, $cntIndexes);

        $this->context->dump = json_encode($indexesArr);

        for ($i = 0, $j = 0; $i < 5; $i++) {
            if ($j < $cntIndexes && $i == (int) $indexesArr[$j]) {
                $this->context->userCards[$i] = $newCards[$j];
                ++$j;
            }
        }
        $this->context->saveUserCards();
    }

    private function extractUserCardsFromRedis(): array
    {
        $data = Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ':userCards');
        return explode(',', $data);
    }
}

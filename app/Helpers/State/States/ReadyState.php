<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class ReadyState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.initMessage');
        $this->context->buttons    = ['startGame'];
    }

    public function waitingOpponentUser()
    {}

    public function connectionOpponentUser()
    {}

    public function connectionCurrentUser()
    {}

    public function startGame()
    {
        $keyStorage               = $this->getKeyStorageForCards();
        $cards                    = new Cards($keyStorage);
        $this->context->userCards = $cards->getFiveCards();
        $this->saveUserCards($this->context->userCards);
        $this->context->updateState('StartedGameState');
    }

    private function getKeyStorageForCards(): string
    {
        return $this->context->roomName . ':cards';
    }

    private function saveUserCards($userCards)
    {
        $data = implode(",", $userCards);
        Redis::set($this->context->roomName . ':' . $this->context->currentUser->id . ':userCards', $data);
    }
}

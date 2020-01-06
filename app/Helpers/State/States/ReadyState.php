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
        // проверить - если 1-ый игрок не получил карты
        // то перевести 2-го игрока в состояние ожидания
        if ($this->context->role === 'opponentUser' && $this->getOpponentState() !== 'StartedGameState') {
            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
                ['user' => $this->context->opponentUser->name]);
            $this->context->updateState('WaitingState', $waitingMessage);
            return;
        }

        $keyStorage = $this->getKeyStorageForCards();
        $cards      = new Cards($keyStorage);

        if ($this->context->role === 'currentUser') {
            $this->context->userCards = $cards->getFiveCards(0, 5);
        } else {
            $this->context->userCards = $cards->getFiveCards(5, 5);
        }
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

    private function getOpponentState(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->opponentUser->id . ':state');
    }
}

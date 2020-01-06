<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class StartedGameState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->buttons    = ['changeCards', 'notChange'];
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.startedMessage');
        $this->context->userCards  = $this->extractUserCardsFromRedis();

        \App\Events\SendStartedGameStatus::dispatch();
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

    private function extractUserCardsFromRedis(): array
    {
        $data = Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ':userCards');
        return explode(',', $data);
    }
}

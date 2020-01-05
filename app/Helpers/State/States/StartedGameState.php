<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class StartedGameState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->buttons    = ['changeCards', 'notChange'];
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.startedMessage');
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

}

<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class NewRoundState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.initMessage');
    }

    /**
     * Начать партию
     */
    public function startGame()
    {
        $waitingMessage = __(
            'main_page_content.gamePage.statusMessages.waitingMessage2',
            ['user' => $this->context->opponentUser->name]
        );
        $this->context->updateState('WaitingState', $waitingMessage, 'none', false, false);
    }
}

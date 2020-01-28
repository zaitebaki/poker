<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class NewRoundState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.initMessage');
        // $this->context->buttons    = ['startGame'];
        // $this->context->indicator  = 'ready';
    }

    public function startGame()
    {
        // $this->context->pushStartingBet(5);

        // проверить - если 1-ый игрок не получил карты
        // то перевести 2-го игрока в состояние ожидания

        $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
            ['user' => $this->context->opponentUser->name]);
        $this->context->updateState('WaitingState', $waitingMessage, 'none', false, false);
        // return;

        // if ($this->context->role === 'opponentUser' && $this->context->getOpponentState() !== 'StartedGameState') {
        //     $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
        //         ['user' => $this->context->opponentUser->name]);
        //     $this->context->updateState('WaitingState', $waitingMessage);
        //     return;
        // }

        // $keyStorage = $this->context->getKeyStorageForCards();
        // $cards      = new Cards($keyStorage);

        // if ($this->context->role === 'currentUser') {
        //     $this->context->userCards = $cards->getCards(0, 5);
        //     $this->context->saveUserCards();
        //     $this->context->indicator = 'ready';

        //     $this->context->updateState('StartedGameState');

        // \App\Events\SendStartedGameStatus::dispatch($this->context->roomId);
        // } else {
        //     $this->context->userCards = $cards->getCards(5, 5);
        //     $waitingMessage           = __('main_page_content.gamePage.statusMessages.waitingMessage3',
        //         ['user' => $this->context->opponentUser->name]);
        //     $this->context->saveUserCards();
        //     $buttons = 'changeCards,notChange';
        //     $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
        // }
    }

    public function connectionCurrentUser()
    {}
    public function waitingOpponentUser()
    {}
    public function connectionOpponentUser()
    {}
}

<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;
use App\Helpers\Cards\Cards;


class FinishState extends State
{

    public function __construct($context)
    {
        parent::__construct($context);

        // $this->context->userCards         = $this->context->extractUserCardsFromRedis();
        // $this->context->opponentUserCards = $this->context->extractOpponentUserCardsFromRedis();
        $deckOfCards = [
            '2c', '2b', '2v', '2k',
            '3c', '3b', '3v', '3k',
            '4c', '4b', '4v', '4k',
            '5c', '5b', '5v', '5k',
            '6c', '6b', '6v', '6k',
            '7c', '7b', '7v', '7k',
            '8c', '8b', '8v', '8k',
            '9c', '9b', '9v', '9k',
            'xc', 'xb', 'xv', 'xk',
            'vc', 'vb', 'vv', 'vk',
            'dc', 'db', 'dv', 'dk',
            'kc', 'kb', 'kv', 'kk',
            'tc', 'tb', 'tv', 'tk',
            '1j', '2j',
        ];

        shuffle($deckOfCards);
        $arr = array_slice($deckOfCards, 0, 5);

        $this->context->userCards = $arr;
        $this->context->saveUserCards();
        // $this->context->userCards = ["vc", "tb", "kb", "xk", "kc"];

        $this->context->opponentUserCards = ["tc", "3v", "5k", "8v", "8k"];

        $this->context->buttons = ['equal', 'equalAndAdd', 'gameOver'];
        $this->context->dump    = Cards::whoWinner($this->context->userCards, $this->context->opponentUserCards);
        // $this->context->dump = $arr;

        // if (Card::whoWiner() === $this->context->currentUser->id) {

        // }

        // elseif (Card::whoWiner() === $this->context->opponentUser->id) {

        // }

        // $cards = new Cards($keyStorage);

        // $keyStorage  = $this->context->getKeyStorageForCards();

        // $this->context->statusText = __('main_page_content.gamePage.statusMessages.finishMessage');

        // $this->context->statusText = card->whoWin();

        // $this->context->buttons   = ['addMoney', 'noMoney'];
        // $this->context->userCards = $this->context->extractUserCardsFromRedis();
        // $this->context->indicator = 'ready';
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
    }
    public function check()
    {
    }

    public function equalAndAdd()
    {
    }

    public function equal()
    {
    }

    public function gameOver()
    {
    }
}

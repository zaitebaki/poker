<?php

namespace App\Helpers\Cards;

class Cards
{
    private $deckOfCards = [
        '2c', '2b', '2v', '2k',
        '3c', '3b', '3v', '3k',
        '4c', '4b', '4v', '4k',
        '5c', '5b', '5v', '5k',
        '6c', '6b', '6v', '6k',
        '7c', '7b', '7v', '7k',
        '8c', '8b', '8v', '8k',
        '9c', '9b', '9v', '9k',
        'ac', 'ab', 'av', 'ak',
        'bc', 'bb', 'bv', 'bk',
        'cc', 'cb', 'cv', 'ck',
        'kc', 'kb', 'kv', 'kk',
        'tc', 'tb', 'tv', 'tk',
        'j1', 'j2',
    ];

    public function __construct()
    {
        return $this->deckOfCards;
    }

    public function getFiveCards()
    {
        shuffle($this->deckOfCards);
        return array_slice($this->deckOfCards, 0, 5);
    }
}

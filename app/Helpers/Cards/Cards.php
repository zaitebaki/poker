<?php

namespace App\Helpers\Cards;

use Illuminate\Support\Facades\Redis;

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
        'xc', 'xb', 'xv', 'xk',
        'vc', 'vb', 'vv', 'vk',
        'dc', 'db', 'dv', 'dk',
        'kc', 'kb', 'kv', 'kk',
        'tc', 'tb', 'tv', 'tk',
        '1j', '2j',
    ];

    private $keyStorageForCards;

    public function __construct($keyStorage)
    {
        $this->keyStorageForCards = $keyStorage;

        if ($this->cardsAlreadyExists()) {
            $this->deckOfCards = $this->extractCardsFromRedis();
        } else {
            shuffle($this->deckOfCards);
            $this->saveCards();
        }
    }

    public function getCards(int $startIndex, int $endIndex)
    {
        return array_slice($this->deckOfCards, $startIndex, $endIndex);
    }

    private function saveCards()
    {
        Redis::set($this->keyStorageForCards, implode(',', $this->deckOfCards));
    }

    private function cardsAlreadyExists(): bool
    {
        return Redis::exists($this->keyStorageForCards);
    }

    private function extractCardsFromRedis(): array
    {
        $data = Redis::get($this->keyStorageForCards);
        return explode(',', $data);
    }
}

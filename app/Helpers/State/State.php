<?php

namespace App\Helpers\State;

abstract class State
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct(GamePlay $context)
    {
        $this->context = $context;
    }

    abstract public function connectionCurrentUser();
    abstract public function waitingOpponentUser();

    abstract public function connectionOpponentUser();
    abstract public function startGame();
    // abstract public function getFiveCards();
    // abstract public function replaceCards();
}

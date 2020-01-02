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

    abstract public function connectionSrcUser();
    abstract public function waitingDstUser();

    abstract public function connectionDstUser();
    abstract public function startGame();
    // abstract public function getFiveCards();
    // abstract public function replaceCards();
}

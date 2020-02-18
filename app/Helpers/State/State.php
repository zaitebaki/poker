<?php

namespace App\Helpers\State;

abstract class State
{
    protected $context;

    public function __construct(GamePlay $context)
    {
        $this->context = $context;
    }
}

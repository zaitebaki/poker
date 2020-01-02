<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class WaitingState extends State
{
    public function __construct($context, $watingText)
    {
        parent::__construct($context);

        $this->context->statusText = $watingText;
    }

//     function __construct() {
    //        parent::__construct();
    //    }

    public function waitingDstUser()
    {}

    public function connectionDstUser()
    {}

    public function connectionSrcUser()
    {}
    
    public function startGame()
    {}
}

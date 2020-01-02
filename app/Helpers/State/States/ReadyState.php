<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;

class ReadyState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.initMessage');

    }

    public function waitingDstUser()
    {}

    public function connectionDstUser()
    {}

    public function connectionSrcUser()
    {}

    public function startGame()
    {}
}

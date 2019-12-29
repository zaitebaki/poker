<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

class GameController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->title  = 'Пятикарточный покер';
        $this->layout = env('THEME') . ".route.main";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->content = view(env('THEME') . '.game.index')->render();
        return $this->renderOutput();
    }
}

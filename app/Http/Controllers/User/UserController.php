<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

class UserController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->layout = env('THEME') . ".route.main";
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $this->title   = "Личный кабинет :: " . $this->user->name;
        $this->content = view(env('THEME') . '.home.user')->with(['friends' => $this->user->friends])->render();

        return $this->renderOutput();
    }
}

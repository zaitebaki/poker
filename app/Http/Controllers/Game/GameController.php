<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class GameController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void     */
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
    {}

    public function sendInvitation(Request $request)
    {

        if ($request->isInvitatationForm !== "true") {
            $this->dispatchInviation($request);
        }

        $this->content = view(env('THEME') . '.game.index')->render();
        return $this->renderOutput();
    }

    public function acceptInvitation(Request $request)
    {
        // return 'hello';
        // $id_dst_user = User::where('login', $request->dstUserLogin)->first();
        // $id_dst_user->invitations()->attach($this->user->id);

        // \App\Events\SendInvitation::dispatch($request->srcUserId, $id_dst_user->id);

        $this->content = view(env('THEME') . '.game.index')->render();
        return $this->renderOutput();
    }

    private function dispatchInviation(Request $request)
    {
        $id_dst_user = User::where('login', $request->dstUserLogin)->first();
        $id_dst_user->invitations()->attach($this->user->id);

        \App\Events\SendInvitation::dispatch($request->srcUserId, $id_dst_user->id);
    }
}

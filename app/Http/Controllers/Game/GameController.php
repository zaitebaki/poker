<?php

namespace App\Http\Controllers\Game;

use App\Helpers\State\GamePlay;
use App\Helpers\State\States\ReadyState;
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

    public function sendMessage(Request $request)
    {
        $srcUser = null;
        $dstUser = null;

        if (isset($request->srcUserLogin)) {
            $srcUser = User::where('login', $request->srcUserLogin)->first();
            $dstUser = $this->user;
        }

        if (isset($request->dstUserLogin)) {
            $srcUser = $this->user;
            $dstUser = User::where('login', $request->dstUserLogin)->first();
        }

        // пользователь пригласил в игру
        // другого пользователя
        if ($request->isSrcInvitatationForm === 'true') {
            $game = new Gameplay($this->user, $dstUser, $request);
            $game->connectionSrcUser();
            $statusMessage = $game->getStatusText();
        }

        // пользователя пригласил в игру
        // другой пользователь
        if ($request->isDstInvitatationForm === 'true') {
            $game = new Gameplay($srcUser, $this->user, $request);
            $game->connectionDstUser();
            $statusMessage = $game->getStatusText();
        }

        if ($request->updateState === 'ReadyState') {

            $game          = new Gameplay($srcUser, $dstUser, $request);
            $game->state   = new ReadyState($game);
            $statusMessage = $game->getStatusText();
            // return ['statusMessage' => $statusMessage];

            return $statusMessage;
        }

        $this->content = view(env('THEME') . '.game.index')->with(['statusMessage' => $statusMessage])->render();
        return $this->renderOutput();
    }

    // public function acceptInvitation(Request $request)
    // {
    //     $this->content = view(env('THEME') . '.game.index')->render();
    //     return $this->renderOutput();
    // }

    // private function dispatchInviation(Request $request)
    // {
    //     $id_dst_user = User::where('login', $request->dstUserLogin)->first();
    //     $id_dst_user->invitations()->attach($this->user->id);

    //     \App\Events\SendInvitation::dispatch($request->srcUserId, $id_dst_user->id);
    // }
}

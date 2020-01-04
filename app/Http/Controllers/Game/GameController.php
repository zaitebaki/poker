<?php

namespace App\Http\Controllers\Game;

use App\Helpers\State\GamePlay;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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

    public function invitationMessage(Request $request)
    {
        $userId     = $this->user->id;
        $opponentId = $request->opponentId;

        // инициализировать приглашение в игру
        if ($request->updateState === 'InitState' && $request->sendInvitationRequest === 'true') {

            $roomCreated = 'room:' . $userId . ':' . $opponentId;
            $result      = Redis::command('exists', [$roomCreated]);
            $roomName    = '';
            if ($result === 0) {

                $countRoomsNow = Redis::get('room:count');

                $roomName = 'room_' . ((int) $countRoomsNow + 1);
                Redis::set($roomCreated, $roomName);

                Redis::set($roomName . ':idUserCurrent', $userId);
                Redis::set($roomName . ':idUserOpponent', $opponentId);
            }

            if ($result === 1) {
                $roomName = Redis::get('room:' . $userId . ':' . $opponentId);
            }

            Redis::set($roomName . ':' . $userId . ':state', 'InitState');

            $game = new Gameplay($this->user, $roomName, $request);
            $game->connectionCurrentUser();

            $roomId = explode("_", $roomName);
            return redirect()->route('sendMessage', ['id' => $roomId[1]])->with(['statusMessage' => $game->getStatusText()]);
        }

        // принять приглашение для начала игры
        if ($request->updateState === 'InitState' && $request->takeInvitationRequest === 'true') {

            $roomName = Redis::get('room:' . $opponentId . ':' . $userId);
            Redis::set($roomName . ':' . $userId . ':state', 'InitState');
            $game = new Gameplay($this->user, $roomName, $request);
            $game->connectionOpponentUser();

            $roomId = explode("_", $roomName);
            return redirect()->route('sendMessage', ['id' => $roomId[1]])->with(['statusMessage' => $game->getStatusText()]);
        }
    }

    public function sendMessage(Request $request)
    {
        if (isset($request->updateState)) {
            $game = new Gameplay($this->user, $request->roomName, $request);
            $game->updateState($request->updateState);

            return $game->getStatusText();
        }

        // $statusMessage = '';
        // $srcUser = null;
        // $dstUser = null;

        // if (isset($request->srcUserLogin)) {
        //     $srcUser = User::where('login', $request->srcUserLogin)->first();
        //     $dstUser = $this->user;
        // }

        // if (isset($request->dstUserLogin)) {
        //     $srcUser = $this->user;
        //     $dstUser = User::where('login', $request->dstUserLogin)->first();
        // }

        // пользователь пригласил в игру
        // другого пользователя
        // if ($request->isSrcInvitatationForm === 'true') {
        //     $game = new Gameplay($this->user, $dstUser, $request);
        //     $game->connectionSrcUser();
        //     $statusMessage = $game->getStatusText();
        // }

        // пользователя пригласил в игру
        // другой пользователь
        // if ($request->isDstInvitatationForm === 'true') {
        //     $game = new Gameplay($srcUser, $this->user, $request);
        //     $game->connectionDstUser();
        //     $statusMessage = $game->getStatusText();
        // }

        // if ($request->updateState === 'ReadyState') {

        //     $game          = new Gameplay($srcUser, $dstUser, $request);
        //     $game->state   = new ReadyState($game);
        //     $statusMessage = $game->getStatusText();
        // return ['statusMessage' => $statusMessage];
        //     return $statusMessage;
        // }

        // if ($request->initAction === 'startGame') {

        //     $game = new Gameplay($srcUser, $dstUser, $request);
        //     $game->startGame();
        // $statusMessage = $game->getStatusText();
        // return ['statusMessage' => $statusMessage];
        //     return $statusMessage;
        // }

        $this->content = view(env('THEME') . '.game.index')->with(['statusMessage' => session('statusMessage')])->render();
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

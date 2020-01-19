<?php

namespace App\Http\Controllers\Game;

use App\Helpers\State\GamePlay;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Helpers\State\States\FinishState;

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
            return redirect()->route('sendMessage', ['id' => $roomId[1]]);
        }

        // принять приглашение для начала игры
        if ($request->updateState === 'InitState' && $request->takeInvitationRequest === 'true') {

            $roomName = Redis::get('room:' . $opponentId . ':' . $userId);
            Redis::set($roomName . ':' . $userId . ':state', 'InitState');
            $game = new Gameplay($this->user, $roomName, $request);
            $game->connectionOpponentUser();

            $roomId = explode("_", $roomName);
            return redirect()->route('sendMessage', ['id' => $roomId[1]]);
        }
    }

    /**
     * Основная функция обмена сообщениями между игроками
     */
    public function sendMessage(Request $request, $room_id)
    {
        // перезагрузка и редирект страницы с игрой
        if ($request->isMethod('get')) {
            return $this->handleGetRequest($request, $room_id);
        }

        if ($request->isMethod('post')) {

            // обновить состояние в GamePlay
            if (isset($request->updateState)) {
                return $this->updateStateHandle($request);
            }

            // инициировать действие в GamePlay
            if (isset($request->initAction)) {
                return $this->handleInitAction($request);
            }
        }
    }

    /**
     * Обновить "состояние" пользователя в игре
     */
    private function updateStateHandle($request)
    {
        $game = new Gameplay($this->user, $request->roomName, $request);
        $game->updateState($request->updateState);

        // конец игры
        if ($request->updateState === 'FinishState') {
            return json_encode(array(
                'user'           => $game->currentUser,
                'gameParameters' => $game->getFinishGameParameters()));
        }

        return json_encode(array('gameParameters' => $game->getGameParameters()));
    }

    /**
     * Инициализировать действие пользователя в игре
     */
    private function handleInitAction(Request $request)
    {
        // инициализировать следующую партию
        // после завершение предыдущей
        if ($request->initAction === 'nextRound') {
            
            FinishState::then($this->user, $request->roomName);
            $game = new Gameplay($this->user, $request->roomName, $request);
            $game->startGame();

            return json_encode(array('gameParameters' => $game->getGameParameters()));
        }

        $game   = new Gameplay($this->user, $request->roomName, $request);
        $method = $request->initAction;
        $game->$method();

        // конец игры
        if ($request->initAction === 'equal' || $request->initAction === 'gameOver') {
            return json_encode(array(
                'user'           => $game->currentUser,
                'gameParameters' => $game->getFinishGameParameters()));
        }

        return json_encode(array('gameParameters' => $game->getGameParameters()));
    }

    /**
     * Обработка get-запроса /game
     */
    private function handleGetRequest(Request $request, $room_id)
    {
        $game = new Gameplay($this->user, 'room_' . $room_id, $request);

        if ($game->state instanceof \App\Helpers\State\States\FinishState) {
            $gameParameters = $game->getFinishGameParameters();
        } else {
            $gameParameters = $game->getGameParameters();
        }

        $this->content = view(env('THEME') . '.game.index')->with(['gameParameters' => $gameParameters])->render();
        return $this->renderOutput();
    }
}

<?php

namespace App\Http\Controllers\Game;

use App\Exceptions\UnauthorizedRoomAccessException;
use App\Helpers\State\GamePlay;
use App\Helpers\State\States\FinishState;
use App\Http\Controllers\Controller;
use App\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class GameController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void     */

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->title  = 'Пятикарточный покер';
        $this->layout = env('THEME') . ".route.main";
    }

    /**
     * Пригласить пользователя-оппонента начать партию
     */
    public function invitationMessage(Request $request)
    {
        $userId     = $this->user->id;
        $opponentId = $request->opponentId;

        // инициализировать приглашение в игру
        if ($request->updateState === 'InitState' && $request->sendInvitationRequest === 'true') {
            $roomCreated = 'room:' . $userId . ':' . $opponentId;
            $result      = Redis::command('exists', [$roomCreated]);
            $roomName    = '';

            // создать "игровую комнату",
            // если она еще не была создана
            if ($result === 0) {
                $countRoomsNow = Redis::get('room:count');

                $newRoomNumber = (int) $countRoomsNow + 1;
                $roomName      = 'room_' . $newRoomNumber;

                Redis::set($roomCreated, $roomName);
                Redis::set($roomName . ':' . $userId, 'ok');
                Redis::set($roomName . ':' . $opponentId, 'ok');
                Redis::set('room:count', $newRoomNumber);

                Redis::set($roomName . ':idUserCurrent', $userId);
                Redis::set($roomName . ':idUserOpponent', $opponentId);
            }

            // получить имя игровой комнаты,
            // если она уже создана
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
     * Списать долг
     */
    public function cancelPayment(Request $request)
    {
        $paymentData = json_decode($request->data);

        // удалить "финансовое" сообщение у текущего пользователя
        $paymentId  = $paymentData->idPayment;
        $payment    = $this->user->payments()->get();
        $payment    = $payment->where('id', $paymentId)->first();
        $opponentId = $payment->opponent_user_id;

        $this->user->payments()->detach($paymentId);
        Payment::destroy($paymentId);

        // удалить "финансовое" сообщение у пользователя-оппонента
        $opponentUser    = User::find($opponentId);
        $paymentOpponent = $opponentUser->payments()->get();
        $paymentOpponent = $paymentOpponent->where('opponent_user_id', $this->user->id)->first();

        $paymentId = $paymentOpponent->id;

        $opponentUser->payments()->detach($paymentId);
        Payment::destroy($paymentId);

        return redirect()->back()->with(['status' => 'success', 'sessionStatusUserLogin' => $opponentUser->login]);
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
            $res = FinishState::then($this->user, $request->roomName);

            // return $res;
            $game = new Gameplay($this->user, $request->roomName, $request);
            $game->startGame();
            return json_encode(array('gameParameters' => $game->getGameParameters()));
        }

        $game   = new Gameplay($this->user, $request->roomName, $request);
        $method = $request->initAction;
        $game->$method();

        // конец игры
        if ($request->initAction === 'equal' ||
            $request->initAction === 'gameOver' ||
            $request->initAction == 'opponentCheck') {
            return json_encode(array(
                'user'           => $game->currentUser,
                'gameParameters' => $game->getFinishGameParameters()));
        }

        return json_encode(array('gameParameters' => $game->getGameParameters()));
    }

    /**
     * Обработка get-запроса /game/room/{room_id}
     */
    private function handleGetRequest(Request $request, $room_id)
    {
        if (!$this->isUserBelongRoom($room_id)) {
            throw new UnauthorizedRoomAccessException("Неавторизованная попытка доступа к комнате $room_id!", 1);
        }

        $game = new Gameplay($this->user, 'room_' . $room_id, $request);

        if ($game->state instanceof \App\Helpers\State\States\FinishState) {
            $gameParameters = $game->getFinishGameParameters();
        } else {
            $gameParameters = $game->getGameParameters();
        }

        $this->content = view(env('THEME') . '.game.index')->with(['gameParameters' => $gameParameters])->render();
        return $this->renderOutput();
    }

    /**
     * Проверить существует ли в БД Redis текущая комната
     */
    private function isUserBelongRoom($room_id)
    {
        $roomName = 'room_' . $room_id;
        if (Redis::exists($roomName . ':' . $this->user->id)) {
            return true;
        }
        return false;
    }

    /**
     * Закончить сеанс игры
     */
    public function finishGameSession(Request $request, $room_id)
    {

        $roomName = 'room_' . $room_id;
        $userId   = $this->user->id;
        $userName = $this->user->login;

        // удалить данные о текущем сеансе игры из бд Redis
        $opponentUserId = $this->getOpponentIdFromRedis($roomName, $userId);
        FinishState::removeUserDataFromRedis($roomName, $userId);
        FinishState::removeUserDataFromRedis($roomName, $opponentUserId);
        FinishState::removeCommonDataFromRedis($roomName);

        FinishState::removeAllRoomDataFromRedis($roomName, $userId, $opponentUserId);

        \App\Events\SendFinishGameSessionStatus::dispatch($room_id, $userName);
    }

    /**
     * Получить id пользователя-оппонента
     */
    private function getOpponentIdFromRedis($roomName, $userId)
    {
        $id = Redis::get($roomName . ':idUserCurrent');

        if ($userId === (int) $id) {
            return Redis::get($roomName . ':idUserOpponent');
        } else {
            return $id;
        }
    }
}

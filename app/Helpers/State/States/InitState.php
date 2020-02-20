<?php

namespace App\Helpers\State\States;

use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class InitState extends State
{

    /**
     * Подключение текущего пользователя к игре
     */
    public function connectionCurrentUser()
    {
        $waitingMessage =
            __(
                'main_page_content.gamePage.statusMessages.waitingMessage',
                ['user' => $this->context->opponentUser->name]
            );
        $this->context->updateState('WaitingState', $waitingMessage);

        // сохранить состояние кнопки "продолжить" (кнопка доступна)
        $this->saveStartButtonIndicator($this->context->opponentUser->id);

        // сохранить флаг "Начало игры"
        $this->saveStartGameFlag();
        $this->context->dispatchInvitation();
    }

    /**
     * Подкючение пользователя-оппонента к игре
     */
    public function connectionOpponentUser()
    {
        $this->context->updateState('ReadyState');
        \App\Events\SendReadyStatus::dispatch($this->context->roomId);
    }

    /**
     * Сохранить состояние кнопки "Начать игру"
     * состояние - НЕДОСТУПНА
     */
    private function saveStartButtonIndicator($userId)
    {
        Redis::set($this->context->roomName . ':' . $userId . ':startButtonIndicator', 'ok');
    }

    /**
     * Сохранить состояние кнопки "Начать игру"
     * состояние - НЕДОСТУПНА
     */
    private function saveStartGameFlag()
    {
        Redis::set($this->context->roomName . ':isStartGameFlag', 'ok');
    }
}

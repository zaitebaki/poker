<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class ReadyState extends State
{
    public function __construct($context)
    {
        parent::__construct($context);
        $this->context->statusText = __('main_page_content.gamePage.statusMessages.initMessage');
        $this->context->buttons    = ['startGame'];
        $this->context->indicator  = 'ready';
    }

    /**
     * Начало партии
     */
    public function startGame()
    {
        $this->context->pushStartingBet(5);

        // проверить - если 1-ый игрок не получил карты
        // то перевести 2-го игрока в состояние ожидания
        // if ($this->context->role === 'opponentUser' && $this->context->getOpponentState() === 'ReadyState') {
        //     $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
        //         ['user' => $this->context->opponentUser->name]);
        //     $this->context->updateState('WaitingState', $waitingMessage);
        //     return;
        // }

        $keyStorage = $this->context->getKeyStorageForCards();
        $cards      = new Cards($keyStorage);

        // текущий пользователь начинает игру
        if ($this->context->role === 'currentUser') {
            $this->context->userCards = $cards->getCards(0, 5);
            $this->context->saveUserCards();

            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
                ['user' => $this->context->opponentUser->name]);
            $this->context->updateState('WaitingState', $waitingMessage, "changeCards,notChange");
            if ($this->isStartGameFlag()) {
                // сделать кнопку "Начать игру" доступной
                $this->delStartButtonIndicator($this->context->currentUser->id);
                \App\Events\SendUpdateIndicatorStartButtonStatus::dispatch($this->context->roomId);
            } else {
                \App\Events\SendStartedGameStatus::dispatch($this->context->roomId);
            }
        } else {
            $this->context->userCards = $cards->getCards(5, 5);
            $waitingMessage           = __('main_page_content.gamePage.statusMessages.waitingMessage3',
                ['user' => $this->context->opponentUser->name]);
            $this->context->saveUserCards();
            $buttons = 'changeCards,notChange';
            $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);

            if ($this->isStartGameFlag()) {
                $this->delStartGameFlag();
            }

            \App\Events\SendBettingStatus::dispatch($this->context->roomId);
        }
    }

    /**
     * Удалить состояние кнопки "Начать игру"
     * состояние - ДОСТУПНА
     */
    private function delStartButtonIndicator($userId)
    {
        Redis::del($this->context->roomName . ':' . $userId . ':startButtonIndicator');
    }

    /**
     * Получить значение флага -
     * доступен ли флаг активности кнопки "Начать игру"
     */
    private function isStartGameFlag()
    {
        $flag = Redis::exists($this->context->roomName . ':isStartGameFlag');
        if ($flag === 0) {
            return false;
        }
        return true;
    }

    /**
     * Удалить флаг начала игры
     */
    private function delStartGameFlag()
    {
        Redis::del($this->context->roomName . ':isStartGameFlag');
    }

    public function waitingOpponentUser()
    {
    }

    public function connectionOpponentUser()
    {
    }

    public function connectionCurrentUser()
    {
    }

    public function changeCards()
    {
    }

    public function addMoney()
    {
    }

    public function check()
    {
    }

    public function equalAndAdd()
    {
    }

    public function equal()
    {
    }

    public function gameOver()
    {
    }

    public function then()
    {
    }
}

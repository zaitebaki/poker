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
        $keyStorage = $this->context->getKeyStorageForCards();
        $cards      = new Cards($keyStorage);

        $winnerId = $this->context->getWinnerIdFromRedis();
        $userId   = $this->context->currentUser->id;

        $whoStartRound = $this->whoStartRound();

        // текущий пользователь начинает игру
        if ($this->context->role === 'currentUser') {

            $this->context->userCards = $cards->getCards(0, 5);
            $this->context->saveUserCards();

            $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage2',
                ['user' => $this->context->opponentUser->name]);
            $this->context->updateState('WaitingState', $waitingMessage, "changeCards,notChange");
            if ($this->isStartGameFlag()) {
                // сделать кнопку "Начать игру" доступной
                $this->delStartButtonIndicator($userId);
                \App\Events\SendUpdateIndicatorStartButtonStatus::dispatch($this->context->roomId);
            } else {
                // if ((int) $winnerId === $userId) {
                //     $this->context->updateState('StartedGameState');
                // } else {
                if ($whoStartRound === 'winner') {
                    $this->context->updateState('StartedGameState');
                } elseif ($whoStartRound === 'looser') {
                    \App\Events\SendStartedGameStatus::dispatch($this->context->roomId);
                }
            }
        } else {
            $this->context->dump      = $userId;
            $this->context->userCards = $cards->getCards(5, 5);
            $waitingMessage           = __('main_page_content.gamePage.statusMessages.waitingMessage3',
                ['user' => $this->context->opponentUser->name]);
            $this->context->saveUserCards();
            $buttons = 'changeCards,notChange';
            $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);

            if ($this->isStartGameFlag()) {
                $this->delStartGameFlag();
                \App\Events\SendBettingStatus::dispatch($this->context->roomId);
            } else {

                if ($whoStartRound === 'winner') {
                    \App\Events\SendStartedGameStatus::dispatch($this->context->roomId);
                } elseif ($whoStartRound === 'looser') {
                    \App\Events\SendBettingStatus::dispatch($this->context->roomId);
                }
                // if ((int) $winnerId !== $userId) {

                // } else {

                // победитель - current user, первым ходит - opponent user // 1,2
                // \App\Events\SendStartedGameStatus::dispatch($this->context->roomId);
                // }
            }

            //     $waitingMessage = __('main_page_content.gamePage.statusMessages.waitingMessage3',
            //         ['user' => $this->context->opponentUser->name]);
            //     $this->context->saveUserCards();
            //     $buttons = 'changeCards,notChange';
            //     $this->context->updateState('WaitingState', $waitingMessage, $buttons, true);
            //     \App\Events\SendBettingStatus::dispatch($this->context->roomId);
            // } else {
            // $this->context->updateState('StartedGameState');
            // }
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
     * Получить значение поля
     * кто начал раунд? - winner|looser
     */
    private function whoStartRound()
    {
        return Redis::get($this->context->roomName . ':whoStartRound');
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

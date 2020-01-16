<?php

namespace App\Helpers\State\States;

use App\Helpers\Cards\Cards;
use App\Helpers\State\State;
use Illuminate\Support\Facades\Redis;

class FinishState extends State
{

    public function __construct($context)
    {
        parent::__construct($context);

        $this->context->userCards         = $this->context->extractUserCardsFromRedis();
        $this->context->opponentUserCards = $this->context->extractOpponentUserCardsFromRedis();

        // получить итоговые комбинации и очки
        if (!$this->isResultsAlreadyAnnounced()) {
            $gameBones = Cards::getCombintationsAndPoits($this->context->userCards, $this->context->opponentUserCards);
            $this->summarizeGameResults($gameBones);
        }
        
        $this->context->userCombination     = $this->getUserCombinationFromRedis();
        $this->context->opponentCombination = $this->getOpponentCombinationFromRedis();
        $this->context->userPoints          = $this->getUserPointsFromRedis();
        $this->context->opponentPoints      = $this->getOpponentPointsFromRedis();

        $this->context->money        = $this->context->extractMoney();
        $this->context->bankMessages = $this->context->extractBankMessages();

        // вычислить результат партии,
        // если игра закончилась дропом карт одним из игроков
        $statusGame = $this->getEndStatusGame();
        if ($statusGame !== false) {
            if($statusGame === "drop") {
                $loseMoney = $this->getLoseMoney();
                $this->context->statusText = __('main_page_content.gamePage.statusMessages.gameOverMessage2',
                ['money' => $loseMoney]);
                $this->context->isVictory = -1;
            }
            elseif ($statusGame === "winDrop") {
                $victoryMoney = getVictoryMoney();
                $this->context->statusText = __('main_page_content.gamePage.statusMessages.gameOverMessage1',
                ['user' => $this->context->opponentUser->name,'money' => $victoryMoney]);
                $this->context->isVictory = 1;
            }
        }

        // вычислить результат игры,
        // если необходимо рассчитать комбинацию и очки
        else {
            $winnerId = $this->getWinnerIdFromRedis();

            // победа
            if ($winnerId === (string) $this->context->currentUser->id) {
                $this->context->statusText = __('main_page_content.gamePage.statusMessages.winFinishMessage',
                    ['money' => $this->context->money / 2]);
                $this->context->isVictory = 1;

                // проигрыш
            } elseif ($winnerId === (string) $this->context->opponentUser->id) {
                $this->context->statusText = __('main_page_content.gamePage.statusMessages.loseFinishMessage',
                    ['money' => $this->context->money / 2]);
                $this->context->isVictory = -1;

                // ничья
            } elseif ($winnerId === '0') {
                $this->context->statusText = __('main_page_content.gamePage.statusMessages.drawFinishMessage',
                    ['money' => $this->context->money / 2]);
                $this->context->isVictory = 0;
            }
        }

        $this->context->buttons = ['then'];
        // $this->context->dump    = $this->context->opponentUser->id;
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

    public function startGame()
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

    /**
     * Проверить рассчитаны ли результаты игры
     */
    private function isResultsAlreadyAnnounced(): bool
    {
        return Redis::exists($this->context->roomName . ":winner");
    }

    /**
     * Определить победителя и сохранить результаты
     */
    private function summarizeGameResults(array $gameBones): void
    {
        // определить победителя
        $currenUserPoints   = $gameBones['points']['currentUserPoints'];
        $opponentUserPoints = $gameBones['points']['opponentUserPoints'];

        if ($currenUserPoints === $opponentUserPoints) {
            $this->context->saveWinner('0');
        } elseif ($currenUserPoints > $opponentUserPoints) {
            $this->context->saveWinner($this->context->currentUser->id);
        } elseif ($currenUserPoints < $opponentUserPoints) {
            $this->context->saveWinner($this->context->opponentUser->id);
        }

        $this->context->saveUserPoints($currenUserPoints, $this->context->currentUser->id);
        $this->context->saveUserPoints($opponentUserPoints, $this->context->opponentUser->id);

        // сохранить комбинации игроков
        $currentUserCombination  = $gameBones['combinations']['currentUserCombination'];
        $opponentUserCombination = $gameBones['combinations']['opponentUserCombination'];

        $this->saveUserCombination($currentUserCombination, $this->context->currentUser->id);
        $this->saveUserCombination($opponentUserCombination, $this->context->opponentUser->id);
    }

    /**
     * Извлечь комбинацию текущего пользователя
     */
    private function getUserCombinationFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":combination");
    }

    /**
     * Извлечь комбинацию пользователя-оппонента
     */
    private function getOpponentCombinationFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->opponentUser->id . ":combination");
    }

    /**
     * Извлечь очки текущего пользователя
     */
    private function getUserPointsFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":points");
    }

    /**
     * Извлечь очки пользователя-оппонента
     */
    private function getOpponentPointsFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->opponentUser->id . ":points");
    }

    /**
     * Извлечь id победителя
     */
    private function getWinnerIdFromRedis(): string
    {
        return Redis::get($this->context->roomName . ':winner');
    }

    /**
     * Извлечь статус окончания игры при дропе
     */
    public function getEndStatusGame(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":endGameStatus");
    }

    /**
     * Извлечь количество проигранных денег при дропе
     */
    public function getLoseMoney(): string
    {
        return Redis::get($this->context->roomName . ':' . $this->context->currentUser->id . ":dropGameMoney");
    }
}



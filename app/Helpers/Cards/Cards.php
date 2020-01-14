<?php

namespace App\Helpers\Cards;

use Illuminate\Support\Facades\Redis;

class Cards
{
    private $deckOfCards = [
        '2c', '2b', '2v', '2k',
        '3c', '3b', '3v', '3k',
        '4c', '4b', '4v', '4k',
        '5c', '5b', '5v', '5k',
        '6c', '6b', '6v', '6k',
        '7c', '7b', '7v', '7k',
        '8c', '8b', '8v', '8k',
        '9c', '9b', '9v', '9k',
        'xc', 'xb', 'xv', 'xk',
        'vc', 'vb', 'vv', 'vk',
        'dc', 'db', 'dv', 'dk',
        'kc', 'kb', 'kv', 'kk',
        'tc', 'tb', 'tv', 'tk',
        '1j', '2j',
    ];

    const MASKS_FOR_VALUES = [
        'x' => 10,
        'v' => 11,
        'd' => 12,
        'k' => 13,
        't' => 14,
        'j' => 0,
    ];

    const MASKS_FOR_COMBINATIONS = [
        'j1v4'       => 'POKER', 'j2v3'        => 'POKER',
        'v4w1'       => 'KARE', 'j1v3w1'       => 'KARE', 'j2v2w1'     => 'KARE',
        'v3w2'       => 'FULLHOUSE', 'j1v2w2'  => 'FULLHOUSE',
        'v3w1x1'     => 'TROIKA', 'j1v2w1x1'   => 'TROIKA', 'j2v1w1x1' => 'TROIKA',
        'v2w2x1'     => 'TWO_PAIRS',
        'v2w1x1y1'   => 'DVOIKA', 'j1v1w1x1y1' => 'DVOIKA',
        'v1w1x1y1z1' => 'WASTE',
    ];

    const POINTS_FOR_COMBINATIONS = [
        'POKER'       => 9,
        'STREETFLASH' => 8,
        'KARE'        => 7,
        'FULLHOUSE'   => 6,
        'FLASH'       => 5,
        'STREET'      => 4,
        'TROIKA'      => 3,
        'TWO_PAIRS'   => 2,
        'DVOIKA'      => 1,
        'WASTE'       => 0,
    ];

    private static $disOrderNumberForStreet = 0;
    private static $isStreetWithLowTuz = false;

    private $keyStorageForCards;

    public function __construct($keyStorage)
    {
        $this->keyStorageForCards = $keyStorage;

        if ($this->cardsAlreadyExists()) {
            $this->deckOfCards = $this->extractCardsFromRedis();
        } else {
            shuffle($this->deckOfCards);
            $this->saveCards();
        }
    }

    public function getCards(int $startIndex, int $endIndex)
    {
        return array_slice($this->deckOfCards, $startIndex, $endIndex);
    }

    private function saveCards()
    {
        Redis::set($this->keyStorageForCards, implode(',', $this->deckOfCards));
    }

    private function cardsAlreadyExists(): bool
    {
        return Redis::exists($this->keyStorageForCards);
    }

    /**
     * Получить колоду из Redis
     */
    private function extractCardsFromRedis(): array
    {
        $data = Redis::get($this->keyStorageForCards);
        return explode(',', $data);
    }

    /**
     * Определить победителя партии
     */
    public static function getCombintationsAndPoits(array $currentUserHand, array $opponentUserHand): array
    {
        // заменить джокеры
        self::handleJokers($currentUserHand);
        self::handleJokers($opponentUserHand);

        // получить комбинацию для руки
        $currentUserCombination = self::getCombination($currentUserHand);
        $opponentUserCombination = self::getCombination($opponentUserHand);

        // получить очки для комбинации
        $currentUserPoints = self::getPoints($currentUserHand, $currentUserCombination);
        $opponentUserPoints = self::getPoints($opponentUserHand, $opponentUserCombination);

        $currentUserPoints = (float)$currentUserPoints;
        $opponentUserPoints = (float)$opponentUserPoints;

        return [
            'combinations' => [
                'currentUserCombination' => $currentUserCombination,
                'opponentUserCombination' => $opponentUserCombination
            ],
            'points' => [
                'currentUserPoints' => $currentUserPoints,
                'opponentUserPoints' => $opponentUserPoints
            ],
        ];
    }

    /**
     * Заменить джокер 1j => j1
     */
    private static function handleJokers(&$hand)
    {
        foreach ($hand as $index => $card) {
            if ($card === '1j') {
                $hand[$index] = 'j1';
            } elseif ($card === '2j') {
                $hand[$index] = 'j2';
            }
        }
        unset($hand);
    }

    /**
     * Получить количество очков для комбинации
     */
    private static function getPoints($hand, $combination)
    {
        $stringPoints = '';

        $countJokers = self::getCountJokers($hand);
        if ($countJokers > 0 && ($combination === 'STREET' || $combination === 'STREETFLASH')) {
            $stringPoints = self::getPointsForStreet($hand, $countJokers);
        }
        else {
            if ($countJokers > 0 && $combination === 'FLASH') {
                self::getHandForFlash($hand, $countJokers);
            }

            $arrWithoutJokers = self::getArrWithoutJokers($hand);

            $values = self::getValues($arrWithoutJokers);
            $valuesCount = array_count_values($values);
            $valuesCount = self::changeMaskSymbolsOnKeys($valuesCount);

            $arr = [];
            foreach ($valuesCount as $key => $value) {
                $arr[] = implode(",", [$key, $value]);
            }

            usort($arr, "self::cmp");

            $str = '';
            foreach ($arr as $key => $value) {
                $str .= ($key . '-' . $value . ' ');
            }
            
            foreach ($arr as $key => $value) {
                
                $split = explode(',', $value);
                $num = $split[0];

                if (mb_strlen($num) === 1) {
                    $stringPoints .= ('0' . $num);
                }
                else {
                    $stringPoints .= $num;
                }
            }

            if ($combination === 'TWO_PAIRS' || $combination === 'FULLHOUSE') {
                self::getPointsForTwoPairsFullhouse($stringPoints);
            }
        }

        $totalStringPoints = Cards::POINTS_FOR_COMBINATIONS[$combination] . "." . $stringPoints;
        return $totalStringPoints;
    }

    /**
     * Сравнить две карты
     */
    private static function cmp($a, $b) {

        $aSplit = explode(",", $a);
        $bSplit = explode(",", $b);

        if ($aSplit[1] > $bSplit[1]) {
            return -1;
        }
        if ($aSplit[1] < $bSplit[1]) {
            return 1;
        }
        if ($aSplit[1] === $bSplit[1]) {
            return ($aSplit[0] < $bSplit[0]) ? 1: -1;
        }
    }
    /**
     * Получить количество джокеров в "руке"
     */
    private static function getCountJokers(array $hand): int
    {
        $countJokers = 0;
        foreach ($hand as $key => $value) {
            if ($value[0] === 'j') {
                $countJokers++;
            }
        }
        return $countJokers;
    }
    
    /**
     * Получить очки для комбинаций 'TWO_PAIRS' и 'FULLHOUSE'
     */
    private static function getPointsForTwoPairsFullhouse(string &$stringPoints) {
        $firstNum = (int)substr($stringPoints, 0, 2);
        $secondNum = (int)substr($stringPoints, 2, 2);
        $sum = $firstNum + $secondNum;

        if ($sum < 10) {
            $sum = '0' . (string)$sum;
        }
        else {
            $sum = (string) $sum;
        }

        $stringPoints = $sum . substr($stringPoints, 4);
        unset($stringPoints);
    }

    /**
     * Получить очки для комбинации STREET
     */
    private static function getPointsForStreet(array $hand, int $countJokers): string {

        $values = self::getValues($hand);
        
        // удалить туза из комбинации,
        // если туз играет роль единицы
        if (Cards::$isStreetWithLowTuz) {
            $key = array_search('t', $values);
            unset($values[$key]);
        }

        self::changeMaskSymbols($values);

        $maxValueCard = max($values);
        $disOrderValue = Cards::$disOrderNumberForStreet;

        $computedMaxCardValue = $maxValueCard + ($countJokers - $disOrderValue);

        if ($computedMaxCardValue > 14) {
            $computedMaxCardValue = 14;
        }

        return (string)$computedMaxCardValue;
    }

    /**
     * Заменить джокеры в "руке" для кобминации 'FLASH'
     */
    private static function getHandForFlash(&$hand, $countJokers)
    {
        $newValues = [];

        $values = self::getValues($hand);
        self::changeMaskSymbols($values);

        $startIndex = 14;
        for ($i=0; $i <  $countJokers; $i++) { 
            for ($j = $startIndex; $j > 1; $j--) { 
                if (array_search($j, $values) === false) {
                    $newValues[] = $j;
                    $startIndex--;
                    break;
                }
                $startIndex--;
            }
        }

        $i = 0;
        $suit = self::getSuiteCode($hand);
        foreach ($hand as $key => $value) {
            if ($value[0] === 'j') {
                $newKey = array_search((string)$newValues[$i++], Cards::MASKS_FOR_VALUES);
                $hand[$key] = $newKey . $suit;
            }
        }
        unset($hand);
    }

    /**
     * Получить код масти для "руки"
     */
    private static function getSuiteCode($hand) {
        foreach ($hand as $key => $value) {
            if ($value[0] !== 'j') {
                return $value[1];
            }
        }
    }

    /**
     * Определить комбинацию для руки
     */
    private static function getCombination($hand)
    {
        $mask        = self::getMask($hand);
        $combination = Cards::MASKS_FOR_COMBINATIONS[$mask];
        $cntJokers = self::getCountJokers($hand);

        if (($combination === 'WASTE') ||
            ($combination === 'DVOIKA' && $cntJokers > 0) ||
            ($combination === 'TROIKA' && $cntJokers > 1)) {
            $isFlash  = self::isFlash($hand);
            $isStreet = self::isStreet($hand);

            if ($isFlash) {
                $combination = 'FLASH';
            }
            if ($isStreet) {
                $combination = 'STREET';
            }

            // проверка на стрит с наименьшим тузом
            $cntTuz = 0;
            $specialHand = [];
            foreach ($hand as $key => $value) {
                if ($value[0] === 't') {
                    $specialHand[$key] = '1' . $value[1];
                    $cntTuz++;
                }
                else {
                    $specialHand[$key] = $value;
                }
            }

            if ($cntTuz === 1) {
                $isStreet = self::isStreet($specialHand);
                if ($isStreet) {
                    Cards::$isStreetWithLowTuz = true;
                    $combination = 'STREET';
                }
            }

            // проверка на STREETFLASH
            if ($isFlash && $isStreet) {
                $combination = 'STREETFLASH';
            }
        }

        return $combination;
    }

    /**
     * Проверить соответсвует ли "рука" комбинации FLASH
     *
     */
    private static function isFlash($hand)
    {

        $arrWithoutJokers = self::getArrWithoutJokers($hand);

        $suits      = self::getSuits($arrWithoutJokers);
        $suitsCount = array_count_values($suits);

        if (count($suitsCount) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Проверить соответсвует ли "рука" комбинации STREET
     *
     */
    private static function isStreet($hand)
    {

        $values = self::getValues($hand);
        self::changeMaskSymbols($values);
        asort($values);


        $arrWithoutJokers = [];
        foreach ($values as $key => $value) {
            if ($value !== 0) {
                $arrWithoutJokers[] = $value;
            }
        }

        $cntJokers = 0;
        $cnt       = count($arrWithoutJokers);

        if ($cnt === 4) {
            $cntJokers = 1;
        } elseif ($cnt === 3) {
            $cntJokers = 2;
        }

        $disOrder    = 0;
        $curDisOrder = 0;
        for ($i = 1; $i < count($arrWithoutJokers); $i++) {
            $curDisOrder = $arrWithoutJokers[$i] - $arrWithoutJokers[$i - 1] - 1;
            $disOrder += $curDisOrder;
            $curDisOrder = 0;
        }

        if ($disOrder > $cntJokers) {
            return false;
        }
        else {
            // сохраним значение "неупорядоченности"
            // в переменную класса
            Cards::$disOrderNumberForStreet = $disOrder;
            return true;
        }
    }

    /**
     * Получить массив с картами без джокеров
     * ["d", "2", "6", "9", "x"] => [12, 2, 6, 9, 10]
     */
    private static function getArrWithoutJokers(array $hand): array
    {

        $arrWithoutJokers = [];
        foreach ($hand as $key => $value) {
            if ($value[0] !== 'j') {
                $arrWithoutJokers[] = $value;
            }
        }
        return $arrWithoutJokers;
    }


    /**
     * Заменить символы-маски на числовые эквиваленты
     * ["d", "2", "6", "9", "x"] => [12, 2, 6, 9, 10]
     */
    private static function changeMaskSymbols(array &$hand): void
    {
        foreach ($hand as $key => $value) {
            if (isset(Cards::MASKS_FOR_VALUES[$value])) {
                $hand[$key] = Cards::MASKS_FOR_VALUES[$value];
            } else {
                $hand[$key] = (int) $value;
            }
        }
        unset($hand);
    }

        /**
     * Заменить символы-маски на числовые эквиваленты
     * ["d", "2", "6", "9", "x"] => [12, 2, 6, 9, 10]
     */
    private static function changeMaskSymbolsOnKeys(array $hand): array
    {
        $arr = [];
        foreach ($hand as $key => $value) {
            if (isset(Cards::MASKS_FOR_VALUES[$key])) {
                $newKey = (string)Cards::MASKS_FOR_VALUES[$key];
                $arr[$newKey] = $value;
            } else {
                $arr[$key] = (int) $value;
            }
        }

        return $arr;
    }

    /**
     * Получить массив со значениями для комбинации
     * ["tc", "tb", "tk", "j1", "8v"] => 'tttj8'
     */
    private static function getValues(array $hand): array
    {
        $values = [];
        foreach ($hand as $index => $card) {
            $values[] = $card[0];
        }

        return $values;
    }

    /**
     * Получить массив с мастями для комбинации
     * ["tc", "tb", "tk", "j1", "j2"] => 'cbkjj'
     */
    private static function getSuits(array $hand): array
    {
        $values = [];
        foreach ($hand as $index => $card) {
            if ($card[0] === 'j') {
                $values[] = 'j';
            } else {
                $values[] = $card[1];
            }
        }

        return $values;
    }

    /**
     * Получить маску для комбинации
     * ["tc", "tb", "tk", "1j", "8v"] => j1v3w1
     */
    private static function getMask($hand)
    {
        $flags = 'vwxyz';

        $values = self::getValues($hand);

        $valuesCount = array_count_values($values);
        arsort($valuesCount);

        $str = '';
        $arr = [];
        $i   = 0;
        foreach ($valuesCount as $key => $value) {
            if ($key !== 'j') {
                $arr[$flags[$i]] = $value;
                $i++;
            } else {
                $arr[$key] = $value;
            }
        }

        ksort($arr);
        $mask = '';
        foreach ($arr as $key => $value) {
            $mask .= ($key . $value);
        }
        return $mask;
    }
}

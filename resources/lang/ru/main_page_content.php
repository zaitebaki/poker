<?php

return [
    'nameProject' => 'Покер «Зайте Баки»',
    'startPage'   => [

        'loginForm'        => [
            'formCaption'          => 'Вход в аккаунт',
            'inputLoginCaption'    => 'Введите логин',
            'inputPasswordCaption' => 'Введите пароль',
            'formDescription'      => 'Еще не зарегистрированы?',
            'formButtonCaption'    => 'Войти',
            'formOtherAction'      => 'Регистрация',
        ],

        'registrationForm' => [
            'formCaption'          => 'Регистрация',
            'inputNameCaption'     => 'Введите имя',
            'inputLoginCaption'    => 'Придумайте логин',
            'inputPasswordCaption' => 'Придумайте пароль',
            'formButtonCaption'    => 'Зарегистрироваться',
            'formDescription'      => 'Уже есть аккаунт?',
            'formOtherAction'      => 'Войти',
        ],
    ],

    'userPage'    => [
        'invitationCard' => [
            'text'              => 'Пользователь «:name» пригласил вас в игру!',
            'formButtonCaption' => 'Присоединиться',
        ],
        'friends'        => [
            'header'        => 'Друзья',
            'noFriendsText' => 'У вас нет друзей',
            'startGameText' => 'Начать игру',
        ],
        'payments'       => [
            'header'              => 'Финансовый отчет',
            'credit'              => 'Вы должны пользователю :user :money рублей',
            'income'              => 'Пользователь :user должен вам :money рублей',
            'incomeButtonCaption' => 'Списать',
            'incomeAlertMessage'  => 'Вы уверены что хотите списать долг?',
            'successMessage'      => 'Долг для пользователя :user успешно списан',
        ],
    ],

    'gamePage'    => [
        'header'          => [
            'shortNameProject' => '«Зайте Баки»',
            'balance'          => 'Баланс',
        ],
        'buttonsCaptions' => [
            'startButton' => 'Начать игру',
            'changeCards' => 'Поменять карты',
            'notChange'   => 'Не меняю',
            'addMoney'    => 'Добавить :money рублей',
            'noMoney'     => 'чек',
            'equal'       => 'Сравнять :money рублей',
            'equalAndAdd' => 'Сравнять :money1 и добавить :money2',
            'gameOver'    => 'Сбросить карты',
            'then'        => 'Продолжить',
        ],
        'statusMessages'  => [
            'initMessage'             => 'Начальная ставка — 5 рублей. Максимальная ставка — 100 рублей. Нажмите «Начать игру».',
            'waitingMessage'          => 'Ожидание пользователя «:user»...',
            'waitingMessage2'         => ':user берет карты...',
            'waitingMessage3'         => ':user меняет карты...',
            'waitingMessage4'         => ':user делает ставку...',
            'startedMessage'          => 'Выберете карты для замены',
            'bettingMessage1'         => ':user поменял карты. Делайте вашу ставку.',
            'bettingMessage2'         => ':user добавил :money рублей. Ваш ход.',
            'addMoneyMessageCurrent'  => 'Вы добавили :money рублей. Сейчас ходит :user...',
            'addMoneyMessageCurrent2' => 'Вы сравняли :money1 и добавили :money2 рублей. Сейчас ходит :user...',
            'checkMessage'            => 'Вы не добавили деньги в банк. Сейчас ходит :user',
            'checkMessageOpponent'    => ':user сказал «чек». Ваш ход.',
            'equalAndAddMoney'        => ':user сравнял :money1 и добавил еще :money2 рублей. Ваш ход.',
            'equalMoney'              => ':user сравнял :money1 рублей. Определяем победителя...',
            'winFinishMessage'        => 'Победа! Вы выиграли :money рублей. Зайте Баки!',
            'loseFinishMessage'       => 'Проигрыш. Вы проиграли :money рублей.',
            'drawFinishMessage'       => 'Ничья. :money возвращаются вам на счет.',
            'gameOverMessage1'        => 'Победа! :user сбросил карты. Вы выиграли :money рублей.',
            'gameOverMessage2'        => 'Проигрыш. Вы сбросили карты и проиграли :money рублей.',
            'checkLoseFinishMessage'  => 'Проигрыш. :user ответил «чек».  Вы проиграли :money рублей.',
            'checkWinFinishMessage'   => 'Победа! :user ответил «чек».  Вы выиграли :money рублей.',
            'equalLoseFinishMessage'  => 'Проигрыш. :user сравнял ставку.  Вы проиграли :money рублей.',
            'equalWinFinishMessage'   => 'Победа! :user сравнял ставку.  Вы выиграли :money рублей.',
        ],
    ],
];

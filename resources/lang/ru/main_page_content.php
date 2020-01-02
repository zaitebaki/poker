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
    ],

    'gamePage'    => [
        'buttonsCaptions' => [
            'startButton' => 'Начать игру',
        ],
        'statusMessages'  => [
            'initMessage'    => 'Начальная ставка - 5 рублей. Игра готова',
            'waitingMessage' => 'Ожидание пользователя «:user»',
        ],
    ],
];

**Laravel 5.8,
PostgreSQL,
Vue,
UIkit,
Redis*

Онлайн-покер «Зайте Баки»
=============================

<p align="center"><img src="https://a.radikal.ru/a16/2002/7d/26b420454a87.png" width="520"></p>

### Задача

Создать онлайн версию карточный игры «Покер» по правилам [Дро-покера](http://www.poker-wiki.ru/poker/%D0%94%D1%80%D0%BE-%D0%BF%D0%BE%D0%BA%D0%B5%D1%80)

**Основные компоненты системы**

1. Форма входа
2. Форма регистрации
3. Личный кабинет
4. Игровая комната

**Личный кабинет**

<p align="center"><img src="https://b.radikal.ru/b32/2002/28/1d474c26b899.png" width="800"></p>

Личный кабинет должен содержать следующую информацию:

1. Имя пользователя
2. Баланс пользователя в рублях
3. Количество побед / проигрышей
4. Блок с приглашениями присоединиться к игре
5. Список друзей
    * онлайн-пользователи, которых можно пригласить в игру
    * остальные пользователи (оффлайн)
6. Финансовый отчет о задолженностях
    * пользователи «должники», у которых текущий пользователь выиграл деньги
    * пользователи, которым текущий пользователь проиграл деньги

**Игровая комната**

<p align="center"><img src="https://c.radikal.ru/c18/2002/6d/5ffc3a59fc45.png" width="800"></p>

Игровая комната должна содержать следующие элементы:
1. Панель информации
2. Меню
3. Текущий банк
4. Статус-строка
5. Панель управления
6. Карты пользователя

Количество игроков

### Используемые технологии

- Поставленная задача решалась с помощью технологий:
    * Laravel 5.8
    * PostgreSQL
    * Vue
    * UIkit
    * Redis

- Для долговременного хранения данных пользователей использовалась база данных [PostgreSQL](https://www.postgresql.org)

Схема базы данных:

<p align="center"><img src="https://b.radikal.ru/b03/2002/e3/b94dea54323f.png" width="300"></p>

- Для хранения данных игровой сессии использовался база данных [Redis](https://redis.io/)

- Для реализации игрового взаимодействия применялись технологии [Laravel Echo](https://laravel.com/docs/5.8/broadcasting), js-библиотеки [laravel-echo](https://github.com/tlaverdure/laravel-echo-server), [Socket.io](https://socket.io/)

- Для реализации front-end части системы использовался компонентный подход с использованием [Vue](https://vuejs.org)

- Для выполнения запросов к серверу использовалась библиотека [axios](https://github.com/axios/axios)

- Для реализации внешнего вида системы использовался front-end фреймворк [UIkit](https://getuikit.com)

- При разработке использовался ESLint от [Wesbos](https://github.com/wesbos/eslint-config-wesbos), в основе которого лежит [Airbnb JavaScript Style Guide](https://github.com/airbnb/javascript)

- Линтинг vue-компонентов - плагин [eslint-plugin-vue](https://github.com/vuejs/eslint-plugin-vue)

- Линтинг php-файлов - плагин [phpcs](https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs)
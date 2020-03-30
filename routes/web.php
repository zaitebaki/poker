<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', 'Home\IndexController@index')->name('startPage');
Route::post('/login', 'Auth\AuthController@authenticate')->name('authenticate');
Route::post('/logout', 'Auth\AuthController@logout')->name('logout');
Route::post('/register', 'Auth\RegisterController@register')->name('registration');

Route::middleware('auth', 'throttle:60,1')->group(function () {
    Route::get('/home', 'User\UserController@index')->name('userPage');
    Route::post('/game/room', 'Game\GameController@invitationMessage')->name('invitationMessage');
    Route::post('/game/room/{room_id}', 'Game\GameController@sendMessage')->name('sendMessagePost');

    Route::post('/game/cancel_payment', 'Game\GameController@cancelPayment')->name('cancelPayment');
    Route::get('/game/room/{room_id}', 'Game\GameController@sendMessage')->name('sendMessage');

    Route::post(
        '/game/room/{room_id}/finish_game_session',
        'Game\GameController@finishGameSession'
    )->name('finishGameSession');

    Route::get('/game/room', function () {
        abort(404);
    });
    Route::get('/invitation', function () {
        abort(404);
    });
    Route::get('/game/cancel_payment', function () {
        abort(404);
    });
});

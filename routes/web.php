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
Route::post('/login', 'Auth\MyAuthController@authenticate')->name('authenticate');
Route::post('/register', 'Auth\RegisterController@register')->name('registration');

Route::middleware('auth', 'throttle:60,1')->group(function () {
    Route::get('/home', 'User\UserController@index')->name('userPage');
    // Route::post('/invitation', 'User\UserController@sendInvitation')->name('sendInvitations');
    Route::post('/game/room', 'Game\GameController@invitationMessage')->name('invitationMessage');
    Route::post('/game/room/{room_id}', 'Game\GameController@sendMessage')->name('sendMessagePost');

    Route::post('/game/cancel_payment', 'Game\GameController@cancelPayment')->name('cancelPayment');
    Route::get('/game/room/{room_id}', 'Game\GameController@sendMessage')->name('sendMessage');

    Route::get('/game/room', function () {
        abort(404);
    });
    Route::get('/invitation', function () {
        abort(404);
    });
});

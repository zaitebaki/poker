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
Route::post('/login', 'Auth\MyAuthController@authencticate')->name('authencticate');
Route::post('/register', 'Auth\RegisterController@register')->name('registration');

Route::get('/home', 'User\UserController@index')->name('userPage');

Route::post('messages', function (Illuminate\Http\Request $request) {

    App\Events\PrivateChat::dispatch($request->all());
});

// Route::get('/', function () {
//     return view('chat');
// });

// Route::post('messages', function (Illuminate\Http\Request $request) {
//     App\Events\Message::dispatch($request->input('body'));
// });

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

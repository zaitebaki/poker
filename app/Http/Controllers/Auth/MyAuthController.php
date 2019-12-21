<?php

namespace Poker\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use Poker\Http\Controllers\Controller;

class MyAuthController extends Controller
{
    public function authencticate(Request $request)
    {
        session(['typeForm' => 'login']);

        $array    = $request->all();
        $remember = $request->has('remember');

        if (Auth::attempt([
            'login'    => $array['login'],
            'password' => $array['password'],
        ], $remember)) {
            return redirect('/home');
        }

        return redirect()->back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'loginErrors' => 'Неправильно введен логин или пароль!',
            ]);
    }

    /**
     *
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}

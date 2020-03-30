<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authenticate(Request $request)
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

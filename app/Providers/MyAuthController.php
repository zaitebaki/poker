<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class MyAuthController extends Controller
{
    public function authencticate(Request $request)
    {
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
                'login' => 'Данные аутентификации не верны',
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

    // public function register()
    // {

    // }

}

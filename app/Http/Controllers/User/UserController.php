<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;

class UserController extends \App\Http\Controllers\SuperController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->layout = env('THEME') . ".route.main";
    }

    /**
     * Показать личный кабинет
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $payments = $this->user->payments()->with('opponentUser')->get();

        $paymentsData = [];
        $index        = 0;
        foreach ($payments as $payment) {
            $paymentsData[$index]['idPayment']     = $payment->id;
            $paymentsData[$index]['idOpponent']    = $payment->opponentUser->id;
            $paymentsData[$index]['nameOpponent']  = $payment->opponentUser->name;
            $paymentsData[$index]['loginOpponent'] = $payment->opponentUser->login;
            $paymentsData[$index]['moneyValue']    = $payment->value;
            $index++;
        }

        $this->title   = "Личный кабинет :: " . $this->user->name;
        $this->content = view(env('THEME') . '.home.user')
            ->with([
                'friends'  => $this->user->friends,
                'payments' => $paymentsData,
            ])->render();

        return $this->renderOutput();
    }

    /**
     * Инициализировать предложение о начале игры
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function sendInvitation(Request $request)
    // {
    //     $id_dst_user = User::where('login', $request->dstUserLogin)->first();
    //     $id_dst_user->invitations()->attach($this->user->id);

    //     \App\Events\SendInvitation::dispatch($request->srcUserId, $id_dst_user->id);
    // }
}

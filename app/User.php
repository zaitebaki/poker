<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'login', 'password', 'victory', 'gameover', 'balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function friends()
    {
        return $this->belongsToMany('App\User', 'friends', 'user1_id', 'user2_id');
    }

    public function payments()
    {
        return $this->belongsToMany('App\Payment', 'user_payment');
    }

    /**
     * Сохранить текущий заработок пользователя в базе данных
     */
    public function savePaymentValue($opponentUserId, $money)
    {
        $payment = $this->payments()->where('opponent_user_id', $opponentUserId);

        if ($payment->get()->isEmpty()) {
            $this->payments()->create([
                'user_id'          => $this->id,
                'opponent_user_id' => $opponentUserId,
                'value'            => $money,
            ]);
            return;
        }
        $payment->increment('value', $money);
    }
}

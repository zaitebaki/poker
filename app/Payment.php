<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $quarded  = [];
    public $timestamps  = false;
    protected $fillable = [
        'user_id', 'opponent_user_id', 'value',
    ];

    protected $with = ['opponentUser'];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_payment');
    }

    public function opponentUser()
    {
        return $this->belongsTo('App\User', 'opponent_user_id');
    }
}

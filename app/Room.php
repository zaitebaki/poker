<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $quarded = [];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_room');
    }
}

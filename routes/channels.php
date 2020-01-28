<?php

use Illuminate\Support\Facades\Redis;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
 */

// Broadcast::channel('connect', function ($name) {
//     return true;
// });

Broadcast::channel('connect', function ($user) {

    return $user->login;
});

// Broadcast::channel('room.{room_id}', function ($user, $id) {
//     return $user->login;
// });

Broadcast::channel('room-action.{room_id}', function ($user, $roomId) {

    $data = Redis::exists('room_' . $roomId . ':' . $user->id);

    if ($data) {
        return true;
    }

    return false;
});

Broadcast::channel('invitation.{id}', function ($user, $id) {

    if ((int) $user->id === (int) $id) {
        return true;
    }

    return false;
});

<?php

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

Broadcast::channel('invitation.{id}', function ($user, $id) {

    // if ($user->invitations->contains($id)) {
    //     return true;
    // }

    return true;
});

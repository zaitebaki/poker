<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendInvitation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $srcUserLogin;
    public $id_dst_user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($srcUserId, $id_dst_user)
    {
        $user               = User::find($srcUserId);
        $this->srcUserLogin = $user->login;
        $this->id_dst_user  = $id_dst_user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        return new PrivateChannel('invitation.' . $this->id_dst_user);
    }
}

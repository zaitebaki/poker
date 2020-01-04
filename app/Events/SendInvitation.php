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

    public $srcUserId;
    public $dstUserId;
    public $srcUserLogin;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($srcUserId, $dstUserId)
    {
        $user            = User::find($srcUserId);
        $this->srcUserId = $user->id;
        $this->dstUserId = $dstUserId;

        $this->srcUserLogin = $user->login;
        $this->dontBroadcastToCurrentUser();

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        // return new PrivateChannel('invitation.' . $this->srcUserId);
        return new PrivateChannel('invitation.1');

    }
}

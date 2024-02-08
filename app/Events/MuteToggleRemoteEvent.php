<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MuteToggleRemoteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sid;

    public function __construct($sid)
    {
        $this->sid = $sid;
    }

    public function broadcastOn()
    {
        return new Channel('toggle-mute-channel');
    }


    public function broadcastAs()
    {
        return 'toggle-mute-event';
    }


}

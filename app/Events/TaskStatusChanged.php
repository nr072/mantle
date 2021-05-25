<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $channelName;
    public $channelType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $channelName, $channelType = 'public')
    {
        $this->data        = !is_array($data) ? [] : $data;
        $this->channelName = !$channelName    ? '' : $channelName;
        $this->channelType = !$channelType    ? '' : $channelType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->channelName !== '') {
            if ($this->channelType === 'public') {
                return new Channel( $this->channelName );
            }
        }
    }

    public function broadcastWith()
    {
        return $this->data;
    }
}

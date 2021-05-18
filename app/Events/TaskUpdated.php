<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use DB;
use Illuminate\Support\Facades\Log;

class TaskUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tasks;
    public $noteId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($noteId)
    {
        $this->noteId = $noteId;

        $this->tasks = DB::table('tasks')
            ->where('note_id', $noteId)
            ->orderByRaw('is_done, due_time is null, due_time, created_at desc')
            ->select(
                'id',
                'name',
                'due_time as dueTimeUtc',
                'is_done as isDone'
            )
            ->limit(20)
            ->get();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('note.' . $this->noteId);
    }


    public function broadcastWith()
    {
        return [ 'tasks' => $this->tasks ];
    }
}

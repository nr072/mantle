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

class NoteListNumsOfTasksUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notes;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {

        // Counts of "not done" tasks for each note.
        $numsOfNotDoneTasks = DB::table('tasks')
            ->where('is_done', 0)
            ->select('note_id as noteId', DB::raw('COUNT(note_id) as numOfNotDone'))
            ->groupBy('note_id');

        // "Joined" to get other note data (e.g., note name).
        $this->notes = DB::table('notes')
            ->leftJoinSub($numsOfNotDoneTasks, 'nums_of_not_done', function ($join) {
                $join->on('nums_of_not_done.noteId', '=', 'notes.id');
            })
            ->select('id', 'name', 'noteId', 'numOfNotDone')
            // ->limit(20)
            ->get();

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('notes');
    }

    public function broadcastWith()
    {
        return [ 'notes' => $this->notes ];
    }
}

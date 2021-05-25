<?php

namespace App\Listeners;

use App\Events\TaskUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TaskStatusChanged;
use DB;

class UpdateNotBadge
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Emits an event to update the corresponding "NoT" badge on the web
     * page.
     *
     * "NoT" ("numbers of tasks") badges are the badge-like numbers shown
     * beside each note name shown on the note card. When a task's status
     * changes, the "NoT" badge of the corresponding note is updated on
     * the web page.
     *
     * @param  TaskUpdated  $event
     * @return void
     */
    public function handle(TaskUpdated $event)
    {
        $noteId = $event->noteId;
        $numOfPendingTasks = DB::table('tasks')
            ->where('note_id', $noteId)
            ->where('is_done', 0)
            ->count();

        $data = [
            'id' => $noteId,
            'numOfPendingTasks' => $numOfPendingTasks
        ];

        event(
            new TaskStatusChanged(
                $data, // Data to be broadcast
                'notes' // Channel name
            )
        );
    }
}

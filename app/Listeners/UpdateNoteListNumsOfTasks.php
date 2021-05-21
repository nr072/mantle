<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TaskUpdated;
use Illuminate\Support\Facades\Log;
use App\Events\NoteListNumsOfTasksUpdated;

class UpdateNoteListNumsOfTasks
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
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TaskUpdated $event)
    {
        broadcast(new NoteListNumsOfTasksUpdated());
    }
}

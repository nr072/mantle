<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Counts of "not done" tasks for each note.
        $numsOfNotDoneTasks = DB::table('tasks')
            ->where('is_done', 0)
            ->select('note_id as noteId', DB::raw('count(note_id) as numOfPendingTasks'))
            ->groupBy('note_id');

        // "Joined" to get other note data (e.g., note name).
        $notes = DB::table('notes')
            ->leftJoinSub($numsOfNotDoneTasks, 'nums_of_not_done', function ($join) {
                $join->on('nums_of_not_done.noteId', '=', 'notes.id');
            })
            ->select('id', 'name', 'noteId', 'numOfPendingTasks')
            // ->limit(20)
            ->get();

        return $notes;

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'bail|required|string|max:30'
        ]);

        $note = new Note;
        $note->name = $validatedData['name'];
        $note->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        $tasks = $note->tasks;

        $noteData = [
            'id' => $note->id,
            'name' => $note->name,
            'tasks' => []
        ];

        // This array's format should match the format of the array Echo
        // receives via the broadcast message. New or missing properties
        // are fine, but property names should not vary.
        foreach ($tasks as $task) {
            array_push($noteData['tasks'], [
                'id' => $task->id,
                'name' => $task->name,
                'dueTimeUtc' => $task->due_time,
                'isDone' => $task->is_done,
                'noteId' => $task->note_id
            ]);
        }

        return $noteData;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        //
    }
}

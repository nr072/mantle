<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Note::latest('updated_at')
            // ->limit(10)
            ->pluck('name', 'id');

        $notes = [];
        foreach ($data as $id => $name) {
            array_push($notes, [
                'id' => $id,
                'name' => $name
            ]);
        }

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
        //
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

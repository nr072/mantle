<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\TaskUpdated;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Tasks with due times come first, ordered by their due times.
        // 'Done' tasks are last.
        return Task::orderByRaw('is_done, due_time is null, due_time, created_at desc')
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'bail|required|string|max:150',
            'dueTime' => 'bail|nullable|date',
            'noteId' => 'bail|required|exists:notes,id' // Task must belong to an existing note
        ]);

        $task = new Task;

        $task->name = $validatedData['name'];
        $task->note_id = $validatedData['noteId'];

        if (isset($validatedData['dueTime'])) {
            $task->due_time = $validatedData['dueTime'];
        }

        $task->save();

        // A message is broadcast to Echo listening to a public channel.
        broadcast( new TaskUpdated($task->note_id) );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'id' => 'required|bail',
            'name' => 'bail|string|max:150',
            'dueTime' => 'bail|nullable|date',
            'isDone' => 'bail|boolean'
        ]);

        // If keys exists, their values are set/updated.
        if (isset($validatedData['name'])) {
            $task->name = $validatedData['name'];
        }
        if (isset($validatedData['isDone'])) {
            $task->is_done = $validatedData['isDone'];
        }

        // If the 'dueTime' key exists and has 'null' as its value, it
        // means that due time is supposed to be removed. Otherwise, if
        // the key exists, due time is simply updated.
        if (array_key_exists('dueTime', $validatedData)) {
            $task->due_time = (
                $validatedData['dueTime'] == 'null'
                    ? null
                    : $validatedData['dueTime']
            );
        }

        $task->save();

        // A message is broadcast to Echo listening to a public channel.
        broadcast( new TaskUpdated($task->note_id) );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        // A message is broadcast to Echo listening to a public channel.
        broadcast( new TaskUpdated($task->note_id) );
    }
}

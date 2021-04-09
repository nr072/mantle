<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::select('id', 'name', 'due_time')
            ->orderByRaw('due_time is null, due_time')
            ->limit(20)
            ->get();

        return $tasks;
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
            'name' => 'bail|required|string|max:150|unique:tasks',
            'dueTime' => 'bail|nullable|date'
        ]);

        $newTask = Task::create([
            'name' => $validatedData['name'],
            'due_time' => $validatedData['dueTime'] ?? null
        ]);
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
            'id' => 'bail|required|numeric|min:1',
            'dueTime' => 'bail|date'
        ]);

        // If no new due time is provided, the existing one is removed.
        $task->due_time = $validatedData['dueTime'] ?? null;
        $task->save();
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
    }
}

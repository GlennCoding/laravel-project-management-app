<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'projectId' => 'required|integer',
            'task.title' => 'required|string|max:255',
            'task.dueDate' => 'date',
        ]);

        $attributes = [
            'title' => $validated['task']['title'],
            'dueDate' => $validated['task']['dueDate'] ?? null,
            'isDone' => false,
        ];

        $project = $request->user()->projects()->find($validated['projectId']);

        $project->tasks()->create($attributes);

        return redirect("/projects/$project->id");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'dueDate' => 'date',
            'isDone' => 'boolean',
        ]);

        $task->update($validated);

        event(new TaskUpdated($task, $request->user()));

        return redirect("/projects/$task->project_id");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect("/projects/$task->project_id");
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, Request $request): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'body' => 'required|string|max:255',
            'dueDate' => 'date',
        ]);

        $project->addTask($validated['body'], $validated['dueDate'] ?? null,);

        return redirect($project->path());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, Task $task)
    {
        $this->authorize('update', $task->project);

        $validatedData = request()->validate([
            'body' => 'string|max:255',
            'dueDate' => 'date',
            'isDone' => 'boolean',
        ]);

        if (array_key_exists('isDone', $validatedData)) {
            $validatedData['isDone'] ? $task->complete() : $task->incomplete();

            unset($validatedData['isDone']);
        }

        $task->update($validatedData);

        return redirect($project->path());
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('update', $task->project);

        $task->delete();

        return redirect($project->path());
    }
}

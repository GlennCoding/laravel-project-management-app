<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $projects = $request->user()->projects()->latest()->get();

        return Inertia::render('Projects/Index', ['projects' => $projects]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:600',
        ]);

        $request->user()->projects()->create($validated);

        return redirect(route('projects.index'));
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
    public function show(Project $project)
    {
        $this->authorize('update', $project);

        $tasks = $project->tasks()->get();

        return Inertia::render('Projects/Project', ['project' => $project, 'tasks' => $tasks]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return Inertia::render('Projects/Edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string|max:600',
        ]);

        $project->update($validated);

        return redirect(route('projects.show', ['project' => $project]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('manage', $project);

        $project->delete();

        return redirect(route('projects.index'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\UserProjectRoleEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectInvitationsController extends Controller
{
    public function invite(Project $project)
    {
        $this->authorize('manage', $project);

        $validated = request()->validate([
            'email' => 'required|email',
        ]);

        $userToBeInvited = User::where([
            'email' => $validated['email']
        ])->firstOrFail();

        $project->invite($userToBeInvited);

        return redirect($project->path());
    }

    public function leave(Project $project)
    {
        $this->authorize('update', $project);

        $project->leave(request()->user());

        return redirect('/dashboard');
    }
}

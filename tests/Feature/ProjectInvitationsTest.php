<?php

namespace Tests\Feature;

use App\Enums\UserProjectRoleEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectInvitationsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    function non_owners_may_not_invite_users()
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $userToBeInvited = User::factory()->create();

        $assertInvitationForbidden = function () use ($user, $userToBeInvited, $project) {
            $this->actingAs($user)
                ->post("/projects/$project->id/invite", ['userId' => $userToBeInvited->id])
                ->assertStatus(403);
        };

        $assertInvitationForbidden();

        $project->invite($user);

        $assertInvitationForbidden();
    }

    /** @test */
    function a_project_owner_can_invite_a_user()
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->create();
        $userToInvite = User::factory()->create();

        $this->actingAs($project->owner)
            ->post($project->path() . '/invite', [
                'email' => $userToInvite->email
            ])
            ->assertRedirect($project->path());

        $this->assertTrue($project->members->contains($userToInvite));
    }

    /** @test */

    public function a_project_member_can_leaveZ_a_project(): void
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->create();
        $projectMember = User::factory()->create();

        $project->invite($projectMember);

        $project->refresh();
        $this->assertTrue($project->members->contains($projectMember));

        $this->actingAs($projectMember)->post($project->path() . '/leave')->assertRedirect('/dashboard');

        $project->refresh();
        $this->assertFalse($project->members->contains($projectMember));
    }
}

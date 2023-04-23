<?php

namespace Tests\Feature;

use App\Enums\UserProjectRoleEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ManageProjectTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function guests_cannot_manage_projects()
    {
        $project = Project::factory()->create();

        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path() . '/edit')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
    }

    /** @test */
    public function a_user_can_create_a_project(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->signIn($user);

        $attributes = Project::factory(['owner_id' => $user->id])->raw();

        $this->post('/projects', $attributes)->assertRedirect('/projects');
        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    public function a_user_can_view_their_project(): void
    {
        $this->withoutExceptionHandling();

        $project = Project::factory()->create();

        $this->actingAs($project->owner)->get($project->path())->assertSuccessful();
    }

    /** @test */
    public function a_user_can_edit_projects(): void
    {
        $project = Project::factory()->create();

        $attributes = ['title' => 'Changed', 'description' => 'Changed'];

        $this->actingAs($project->owner)
            ->patch($project->path(), $attributes)
            ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    public function a_user_can_delete_projects(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /** @test */
    function unauthorized_users_cannot_delete_projects()
    {
        $project = Project::factory()->create();

        $this->delete($project->path())
            ->assertRedirect('/login');

        $user = $this->signIn();

        $this->delete($project->path())->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)->delete($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->signIn();

        $project = Project::factory()->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->signIn();

        $project = Project::factory()->create();

        $this->patch($project->path())->assertStatus(403);
    }

    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $attributes = Project::factory()->raw(['title' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();

        $attributes = Project::factory()->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

}

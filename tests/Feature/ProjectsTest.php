<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_create_a_project(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];


        $response = $this->actingAs($user)->post('/projects', $attributes);

        $response->assertRedirect('/projects');

        $this->assertDatabaseHas('projects', $attributes);
    }

    /**
     * @test
     */
    public function a_user_can_view_projects(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory()->count(5))->create();

        $this->actingAs($user)->get('/projects')->assertSuccessful();
    }

    /**
     * @test
     */
    public function a_user_can_edit_projects(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->has(Project::factory())->create();

        $firstProjectId = $user->projects()->first()->id;

        $newProjectAttributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph
        ];

        $this->actingAs($user)->get("/projects/$firstProjectId/edit")->assertSuccessful();

        $response = $this->patch("/projects/$firstProjectId", $newProjectAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('projects', $newProjectAttributes);
    }

    /**
     * @test
     */
    public function a_user_can_delete_projects(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory())->create();

        $firstProjectId = $user->projects()->first()->id;

        $response = $this->actingAs($user)->delete("/projects/$firstProjectId");

        $response->assertRedirect();

        $this->assertDatabaseMissing('projects', ['id' => $firstProjectId]);
    }

    /**
     * @test
     */
    public function a_user_can_view_a_specific_project(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory())->create();

        $firstProjectId = $user->projects()->first()->id;

        $response = $this->actingAs($user)->get("/projects/$firstProjectId");

        $response->assertInertia(fn(Assert $page) => $page
            ->has("project")
        );
    }
}

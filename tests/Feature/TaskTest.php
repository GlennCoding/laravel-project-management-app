<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_a_user_can_create_todos(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory())->create();

        $firstProjectId = $user->projects()->first()->id;

        $attributes = [
            'title' => $this->faker->sentence,
            'dueDate' => $this->faker->date,
        ];

        $response = $this->actingAs($user)->post('/tasks', ['projectId' => $firstProjectId, 'task' => $attributes]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $attributes);

        $attributes2 = [
            'title' => $this->faker->sentence,
        ];

        $response2 = $this->actingAs($user)->post('/tasks', ['projectId' => $firstProjectId, 'task' => $attributes2]);

        $response2->assertRedirect();

        $this->assertDatabaseHas('tasks', $attributes2);
    }

    public function test_a_user_can_delete_todos(): void
    {
        $this->withoutExceptionHandling();

        $projectFactory = Project::factory()->has(Task::factory());
        $user = User::factory()->has($projectFactory)->create();

        $firstProject = $user->projects()->first();
        $firstTaskId = $firstProject->tasks()->first()->id;

        $response = $this->actingAs($user)->delete("/tasks/$firstTaskId");

        $response->assertRedirect();

        $this->assertDatabaseMissing('tasks', ['id' => $firstTaskId]);
    }

    public function test_a_user_can_update_todos(): void
    {
        $this->withoutExceptionHandling();

        $projectFactory = Project::factory()->has(Task::factory());
        $user = User::factory()->has($projectFactory)->create();

        $firstProject = $user->projects()->first();
        $firstTask = $firstProject->tasks()->first();


        $newAttributes = [
            'title' => $this->faker->sentence,
            'dueDate' => $this->faker->date,
            'isDone' => !$firstTask->isDone,
        ];
        $oppositeOfCurrentIsDone = !$firstTask->isDone;

        $response = $this->actingAs($user)->put("/tasks/$firstTask->id", $newAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [...$newAttributes, 'id' => $firstTask->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserProjectRoleEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_a_user_can_create_tasks(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $project = Project::factory()->create();
        $user->projects()->attach($project->id, ['role' => UserProjectRoleEnum::OWNER]);

        $firstProjectId = $project->id;

        $attributes = [
            'title' => $this->faker->sentence,
            'dueDate' => $this->faker->dateTime,
        ];

        $response = $this->actingAs($user)->post('/tasks', ['projectId' => $firstProjectId, 'task' => $attributes]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', $attributes);
    }

    public function test_a_user_can_delete_tasks(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'assigned_user_id' => $user->id,
        ]);

        $firstTaskId = $task->id;

        $response = $this->actingAs($user)->delete("/tasks/$firstTaskId");

        $response->assertRedirect();

        $this->assertDatabaseMissing('tasks', ['id' => $firstTaskId]);
    }

    public function test_a_user_can_update_tasks(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'assigned_user_id' => $user->id,
        ]);


        $newAttributes = [
            'title' => $this->faker->sentence,
            'isDone' => !$task->isDone,
        ];

        $response = $this->actingAs($user)->put("/tasks/$task->id", $newAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('tasks', [...$newAttributes, 'id' => $task->id]);
    }

    public function test_saves_completedAt_date_on_task_update(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'assigned_user_id' => $user->id,
            'isDone' => false
        ]);

        $response = $this->actingAs($user)->put("/tasks/$task->id", [
            'isDone' => true
        ]);

        $response->assertRedirect();

        $updatedTask = Task::find($task->id);

        $this->assertNotNull($updatedTask['completedAt']);
    }
}

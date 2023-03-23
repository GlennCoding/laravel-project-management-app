<?php

namespace Tests\Feature;

use App\Events\TaskUpdated;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_saves_a_notification_on_task_completion_event(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory()->has(Task::factory()))->create();

        $firstProject = $user->projects()->first();
        $firstTask = $firstProject->tasks()->first();

        event(new TaskUpdated($firstTask, $user));

        $this->assertDatabaseHas('notifications', ['task_id' => $firstTask->id]);
    }

    public function test_saves_a_notification_on_task_completion(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->has(Project::factory()->has(Task::factory(['isDone' => false])))->create();

        $firstProject = $user->projects()->first();
        $firstTask = $firstProject->tasks()->first();

        $newAttributes = [
            'isDone' => true,
        ];

        $response = $this->actingAs($user)->put("/tasks/$firstTask->id", $newAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', ['task_id' => $firstTask->id]);
    }
}

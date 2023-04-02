<?php

namespace Tests\Feature;

use App\Events\TaskUpdated;
use App\Jobs\CheckOverdueTasks;
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

        $task = Task::factory(['isDone' => true]);
        $user = User::factory()->has(Project::factory()->has($task))->create();

        $firstProject = $user->projects()->first();
        $firstTask = $firstProject->tasks()->first();

        event(new TaskUpdated($firstTask));

        $this->assertDatabaseHas('notifications', ['task_id' => $firstTask->id]);
    }

    public function test_saves_a_notification_on_task_completion(): void
    {
        $this->withoutExceptionHandling();

        $task = Task::factory(['isDone' => false]);
        $user = User::factory()->has(Project::factory()->has($task))->create();

        $firstProject = $user->projects()->first();
        $firstTask = $firstProject->tasks()->first();

        $newAttributes = [
            'isDone' => true,
        ];

        $response = $this->actingAs($user)->put("/tasks/$firstTask->id", $newAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', ['task_id' => $firstTask->id]);
    }

    public function test_saves_a_notification_on_task_forgotten_event(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['isDone' => false, 'user_id' => $user->id, 'project_id' => $project->id, 'dueDate' => date('Y-m-d', strtotime('-1 day'))]);

        $job = new CheckOverdueTasks;
        $job->handle();

        $this->assertDatabaseHas('notifications', ['task_id' => $task->id]);
    }

    public function test_saves_a_notification_on_task_streak_event(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory(5)->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->assertDatabaseHas('notifications', ['task_id' => $tasks->id]);
    }
}

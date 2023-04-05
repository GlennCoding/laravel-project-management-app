<?php

namespace Tests\Feature;

use App\Enums\UserProjectRoleEnum;
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

        $task = Task::factory()->create(['isDone' => true]);

        event(new TaskUpdated($task));

        $this->assertDatabaseHas('notifications', ['task_id' => $task->id]);
    }

    public function test_saves_a_notification_on_task_completion(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory(['isDone' => false, 'assigned_user_id' => $user->id])->create();

        $newAttributes = [
            'isDone' => true,
        ];

        $response = $this->actingAs($user)->put("/tasks/$task->id", $newAttributes);

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', ['task_id' => $task->id]);
    }

    public function test_saves_a_notification_on_task_forgotten_event(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory(['isDone' => false, 'assigned_user_id' => $user->id, 'dueDate' => date('Y-m-d', strtotime('-1 day'))])->create();

        $job = new CheckOverdueTasks;
        $job->handle();

        $this->assertDatabaseHas('notifications', ['task_id' => $task->id]);
    }

//    public function test_saves_a_notification_on_task_streak_event(): void
//    {
//        $this->withoutExceptionHandling();
//
//        $user = User::factory()->create();
//        $project = Project::factory()->create(['user_id' => $user->id]);
//        $doneTasks = Task::factory(4)->create([
//            'user_id' => $user->id,
//            'project_id' => $project->id,
//            'isDone' => true,
//            'completedAt' => now(),
//        ]);
//        $updatingTask = Task::factory()->create([
//            'user_id' => $user->id,
//            'project_id' => $project->id,
//            'isDone' => false,
//            'completedAt' => now(),
//        ]);
//
//        event(new TaskUpdated($updatingTask));
//
//        $this->assertDatabaseHas('notifications', ['task_id' => $updatingTask->id, 'type' => NotificationTypeEnum::TASK_STREAK]);
//    }
}

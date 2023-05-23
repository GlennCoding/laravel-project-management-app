<?php

namespace Tests\Feature;

use App\Enums\NotificationTypeEnum;
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

    /** @test */
    public function saves_a_notification_on_task_completion(): void
    {
        $this->withoutExceptionHandling();

        $task = Task::factory()->create();

        $this->actingAs($task->assignedUser)->patch($task->path(), [
            'isDone' => true,
        ]);

        $this->assertDatabaseHas('notifications', ['project_id' => $task->project->id, 'user_id' => $task->assignedUser->id, 'type' => NotificationTypeEnum::TASK_COMPLETED]);
    }

    /** @test */
    public function saves_a_notification_on_task_forgotten_event(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory(['isDone' => false, 'assigned_user_id' => $user->id, 'dueDate' => date('Y-m-d', strtotime('-1 day'))])->create();

        $job = new CheckOverdueTasks;
        $job->handle();

        $this->assertDatabaseHas('notifications', ['project_id' => $task->project->id, 'user_id' => $task->assignedUser->id, 'type' => NotificationTypeEnum::TASK_OVERDUE]);
    }

    /** @test */
//    public function saves_a_notification_on_task_streak(): void
//    {
//        $this->withoutExceptionHandling();
//
//        $project = Project::factory()->withTasks(5)->create();
//
//        foreach ($project->tasks as $task) {
//            $task->complete();
//        }
//
//        $this->assertDatabaseHas('notifications', ['project_id' => $task->project->id, 'user_id' => $task->project->owner->id, 'type' => NotificationTypeEnum::TASK_STREAK]);
//    }

}

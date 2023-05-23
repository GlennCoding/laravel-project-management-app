<?php

namespace App\Listeners;

use App\Enums\NotificationTypeEnum;
use App\Events\TaskUpdated;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateNotificationOnTaskStreak
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskUpdated $event): void
    {
        $task = $event->task;

        if (!$task->isDone) return;
        if (!$task->isDirty('isDone')) return;

        $completedTaskCountForToday = Task::where('assigned_user_id', $task->assignedUser->id)->whereDate('completedAt', Carbon::today())->count();

//        dd($event);
//        dd($task->assignedUser->id);
//        dd(Task::where('assigned_user_id', $task->assignedUser->id)->get());

        if ($completedTaskCountForToday === 0) return;
        if ($completedTaskCountForToday % 5 != 0) return;


        $notification = new Notification([
            'type' => NotificationTypeEnum::TASK_STREAK,
            'user_id' => $event->task->assignedUser->id,
            'project_id' => $event->task->project->id,
            'message' => "Joooo you got a {$completedTaskCountForToday} streak!!"
        ]);

        $notification->save();
    }
}

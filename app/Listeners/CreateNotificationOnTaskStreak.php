<?php

namespace App\Listeners;

use App\Enums\NotificationTypeEnum;
use App\Events\TaskUpdated;
use App\Models\Notification;
use App\Models\Task;
use Carbon\Carbon;

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

        $completedTaskCountForToday = Task::where('user_id', $task->user_id)->whereDate('completedAt', Carbon::today())->count();

        if ($completedTaskCountForToday === 0) return;
        if ($completedTaskCountForToday % 5 != 0) return;

        $notification = new Notification([
            'type' => NotificationTypeEnum::TASK_STREAK,
            'user_id' => $event->task->user_id,
            'task_id' => $event->task->id,
            'message' => "Joooo you got a {$completedTaskCountForToday} streak!!"
        ]);

        $notification->save();
    }
}

<?php

namespace App\Listeners;

use App\Enums\NotificationTypeEnum;
use App\Events\TaskUpdated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNotificationOnTaskCompleted
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
        if (!$event->task->isDone) return;

        $notification = new Notification([
            'type' => NotificationTypeEnum::TASK_COMPLETED,
            'user_id' => $event->task->user_id,
            'task_id' => $event->task->id,
            'message' => "LFG, you completed *{$event->task->title}*"
        ]);

        $notification->save();
    }
}

<?php

namespace App\Listeners;

use App\Enums\NotificationTypeEnum;
use App\Events\TaskOverdue;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNotificationOnTaskOverdue
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
    public function handle(TaskOverdue $event): void
    {
        $now = Carbon::now();
        $dueDate = Carbon::parse($event->task->dueDate);

        if (!$dueDate->isBefore($now)) return;

        $notification = new Notification([
            'type' => NotificationTypeEnum::TASK_COMPLETED,
            'user_id' => $event->task->user_id,
            'task_id' => $event->task->id,
        ]);

        $notification->save();
    }
}

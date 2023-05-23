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
            'type' => NotificationTypeEnum::TASK_OVERDUE,
            'user_id' => $event->task->assignedUser->id,
            'project_id' => $event->task->project->id,
            'message' => "Bruh, you forgot *{$event->task->title}*"
        ]);

        $notification->save();
    }
}

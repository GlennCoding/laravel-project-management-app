<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case TASK_COMPLETED = 'TASK_COMPLETED';
    case TASK_OVERDUE = 'TASK_OVERDUE';
    case TASK_STREAK = 'TASK_STREAK';
}

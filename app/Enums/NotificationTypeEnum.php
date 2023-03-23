<?php

namespace App\Enums;

enum NotificationTypeEnum: string
{
    case TASK_COMPLETE = 'TASK_COMPLETE';
    case TASK_FORGOTTEN = 'TASK_FORGOTTEN';
}

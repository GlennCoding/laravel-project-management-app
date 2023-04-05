<?php

namespace App\Enums;

enum UserProjectRoleEnum: string
{
    case OWNER = 'OWNER';
    case MEMBER = 'MEMBER';
}

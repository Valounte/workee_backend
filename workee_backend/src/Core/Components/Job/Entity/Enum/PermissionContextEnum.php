<?php

namespace App\Core\Components\Job\Entity\Enum;

enum PermissionContextEnum: string
{
    case TEAM = 'TEAM';
    case USER = 'USER';
    case JOB = 'JOB';
}

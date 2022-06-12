<?php

namespace App\Core\Components\Job\Entity\Enum;

enum PermissionContextEnum: string
{
    case TEAM = 'team';
    case USER = 'user';
}

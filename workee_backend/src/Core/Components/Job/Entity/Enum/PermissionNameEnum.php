<?php

namespace App\Core\Components\Job\Entity\Enum;

enum PermissionNameEnum: string
{
    case CREATE_USER =  'create_user';
    case CREATE_TEAM =  'create_team';
}

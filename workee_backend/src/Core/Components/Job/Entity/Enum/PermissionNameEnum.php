<?php

namespace App\Core\Components\Job\Entity\Enum;

enum PermissionNameEnum: string
{
    case CREATE_USER =  'CREATE_USER';
    case CREATE_TEAM =  'CREATE_TEAM';
    case CREATE_JOB =  'CREATE_JOB';
}

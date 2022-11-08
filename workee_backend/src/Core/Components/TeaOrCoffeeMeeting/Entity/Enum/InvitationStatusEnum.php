<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum;

enum InvitationStatusEnum: string
{
    case ACCEPTED = 'ACCEPTED';
    case DECLINED = 'DECLINED';
    case PENDING = 'PENDING';
}

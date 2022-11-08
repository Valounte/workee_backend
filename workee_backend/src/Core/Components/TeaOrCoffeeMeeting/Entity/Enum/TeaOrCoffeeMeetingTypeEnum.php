<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum;

enum TeaOrCoffeeMeetingTypeEnum: string
{
    case RANDOM_IN_TEAM = 'RANDOM_IN_TEAM';
    case TEAM = 'TEAM';
    case CLASSIC = 'CLASSIC';
}

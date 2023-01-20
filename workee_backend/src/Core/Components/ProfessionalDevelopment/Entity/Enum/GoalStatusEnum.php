<?php

namespace App\Core\Components\ProfessionalDevelopment\Entity\Enum;

enum GoalStatusEnum: string
{
    case TO_DO = 'TO_DO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case DONE = 'DONE';
}

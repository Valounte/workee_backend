<?php

namespace App\Core\Components\ProfessionalDevelopment\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;

class GoalStatusType extends AbstractEnumType
{
    public const NAME = 'goal_status';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return GoalStatusEnum::class;
    }
}

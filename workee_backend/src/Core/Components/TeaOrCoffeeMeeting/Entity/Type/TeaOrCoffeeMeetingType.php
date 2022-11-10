<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;

class TeaOrCoffeeMeetingType extends AbstractEnumType
{
    public const NAME = 'tea_or_coffee_meeting_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return TeaOrCoffeeMeetingTypeEnum::class;
    }
}

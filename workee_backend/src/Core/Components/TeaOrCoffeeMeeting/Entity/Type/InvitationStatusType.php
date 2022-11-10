<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;

class InvitationStatusType extends AbstractEnumType
{
    public const NAME = 'invitation_status_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return InvitationStatusEnum::class;
    }
}

<?php

namespace App\Core\Components\Notification\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;

class NotificationAlertLevelType extends AbstractEnumType
{
    public const NAME = 'notification_alert_level_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return NotificationAlertLevelEnum::class;
    }
}

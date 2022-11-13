<?php

namespace App\Core\Components\Logs\Entity\Type;

use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Infrastructure\Doctrine\Type\AbstractEnumType;

class LogsAlertType extends AbstractEnumType
{
    public const NAME = 'logs_alert_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return LogsAlertEnum::class;
    }
}

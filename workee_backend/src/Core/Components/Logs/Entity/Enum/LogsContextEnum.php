<?php

namespace App\Core\Components\Logs\Entity\Enum;

enum LogsContextEnum: string
{
    case LOGIN = 'LOGIN';
    case NOTIFICATION_SETTINGS = 'NOTIFICATION_SETTINGS';
    case ENVIRONMENT_METRICS_SETTINGS = 'ENVIRONMENT_METRICS_SETTINGS';
}

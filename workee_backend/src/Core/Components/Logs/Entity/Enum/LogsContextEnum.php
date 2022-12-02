<?php

namespace App\Core\Components\Logs\Entity\Enum;

enum LogsContextEnum: string
{
    case LOGIN = 'LOGIN';
    case NOTIFICATION_SETTINGS = 'NOTIFICATION_SETTINGS';
    case ENVIRONMENT_METRICS_SETTINGS = 'ENVIRONMENT_METRICS_SETTINGS';
    case USER = 'USER';
    case TEA_OR_COFFEE_MEETING = 'TEA_OR_COFFEE_MEETING';
    case TEAM = 'TEAM';
    case NOTIFICATION = 'NOTIFICATION';
    case NEWS = 'NEWS';
    case JOB = 'JOB';
    case DAILY_FEEDBACK = 'DAILY_FEEDBACK';
    case ENVIRONMENT_METRICS = 'ENVIRONMENT_METRICS';
}

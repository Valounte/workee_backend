<?php

namespace App\Core\Components\Logs\Entity\Enum;

enum LogsAlertEnum: string
{
    case INFO = '1';
    case WARNING = '2';
    case CRITIC = '3';
}

<?php

namespace App\Core\Components\Logs\Entity\Enum;

enum LogsAlertEnum: string
{
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case CRITIC = 'CRITIC';
}

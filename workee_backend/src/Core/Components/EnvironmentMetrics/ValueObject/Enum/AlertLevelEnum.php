<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum AlertLevelEnum: string
{
    case CONFORM_VALUE =  'CONFORM_VALUE';
    case WARNING_VALUE =  'WARNING_VALUE';
    case ALERT_VALUE = 'ALERT_VALUE';
}

<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedTemperatureMessageEnum: string
{
    case WARNING_TEMPERATURE_TOO_LOW =  'La température est basse. Laissez un peu rentrer la chaleur ou chauffez légèrement la pièce.';
    case WARNING_TEMPERATURE_TOO_HIGH =  'La température est élevée. Refroidissez légèrement la pièce.';
    case ALERT_TEMPERATURE_TOO_LOW = 'La température est très basse. Laissez rentrer la chaleur ou chauffez la pièce.';
    case ALERT_TEMPERATURE_TOO_HIGH = 'La température est très élevée. Refroidissez la pièce.';
    case CONFORM_TEMPERATURE = 'La température est idéale.';
}

<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedTemperatureMessageEnum: string
{
    case WARNING_TEMPERATURE_TOO_LOW =  'La température est basse, laissez un peu rentrer la chaleur ou chauffez légèrement la pièce.';
    case WARNING_TEMPERATURE_TOO_HIGH =  'La température est élevée, refroidissez légèrement la pièce.';
    case ALERT_TEMPERATURE_TOO_LOW = 'La température est très basse, laissez rentrer la chaleur ou chauffez la pièce.';
    case ALERT_TEMPERATURE_TOO_HIGH = 'La température est très élevée, refroidissez la pièce.';
    case CONFORM_TEMPERATURE = 'La température est idéale';
}

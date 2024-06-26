<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedHumidityMessageEnum: string
{
    case WARNING_HUMIDITY_TOO_LOW =  'L\'air est sec. Aérez, utilisez un humidificateur ou baissez le chauffage.';
    case WARNING_HUMIDITY_TOO_HIGH =  'L\'humidité est élevée. Aérez ou ventilez la pièce.';
    case ALERT_HUMIDITY_TOO_HIGH = 'L\'humidité est très élevée. Aérez ou ventilez la pièce.';
    case ALERT_HUMIDITY_TOO_LOW = 'L\'air est très sec. Aérez, utilisez un humidificateur ou baissez le chauffage.';
    case CONFORM_HUMIDITY = 'L\'humidité est idéale.';
}

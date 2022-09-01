<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedLuminosityMessageEnum: string
{
    case WARNING_LUMINOSITY_TOO_LOW =  'La luminosité est trop faible.';
    case WARNING_LUMINOSITY_TOO_HIGH =  'La luminosité est trop élevée.';
    case ALERT_LUMINOSITY_TOO_HIGH = 'La luminosité est très élevée.';
    case ALERT_LUMINOSITY_TOO_LOW = 'La luminosité est très faible.';
    case CONFORM_LUMINOSITY = 'La luminosité est idéale';
}
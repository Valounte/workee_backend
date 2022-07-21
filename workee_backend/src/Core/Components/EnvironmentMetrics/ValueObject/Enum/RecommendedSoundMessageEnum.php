<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedSoundMessageEnum: string
{
    case WARNING_SOUND_TOO_HIGH =  'Le son ambiant est élevé.';
    case ALERT_SOUND_TOO_HIGH = 'Le son ambiant est très élevé.';
    case CONFORM_SOUND = 'Le son ambiant est acceptable';
}

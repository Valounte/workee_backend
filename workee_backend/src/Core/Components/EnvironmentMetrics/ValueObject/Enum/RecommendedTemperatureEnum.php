<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject\Enum;

enum RecommendedTemperatureEnum: string
{
    case RECOMMENDED_FOR_SUMMER_AND_SPRING =  '23°C - 26°C';
    case RECOMMENDED_FOR_WINTER_AND_AUTUMN =  '20°C - 23.5°C';
}

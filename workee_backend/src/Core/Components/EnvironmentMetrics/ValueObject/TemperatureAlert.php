<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject;

use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureMessageEnum;

final class TemperatureAlert
{
    public function __construct(
        private AlertLevelEnum $alertLevel,
        private RecommendedTemperatureMessageEnum $recommendationMessage,
        private ?RecommendedTemperatureEnum $recommendedTemperature = null,
    ) {
    }

    /**
     * Get the value of alertLevel
     */
    public function getAlertLevel(): AlertLevelEnum
    {
        return $this->alertLevel;
    }

    /**
     * Get the value of recommendedTemperature
     */
    public function getRecommendedTemperature(): RecommendedTemperatureEnum
    {
        return $this->recommendedTemperature;
    }

    /**
     * Get the value of recommendationMessage
     */
    public function getRecommendationMessage(): RecommendedTemperatureMessageEnum
    {
        return $this->recommendationMessage;
    }
}

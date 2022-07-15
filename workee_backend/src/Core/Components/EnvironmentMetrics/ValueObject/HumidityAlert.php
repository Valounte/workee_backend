<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject;

use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedHumidityMessageEnum;

final class HumidityAlert
{
    public function __construct(
        private AlertLevelEnum $alertLevel,
        private RecommendedHumidityMessageEnum $recommendationMessage,
        private string $recommendedHumidity = '40% - 60%',
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
     * Get the value of recommendedHumidity
     */
    public function getRecommendedHumidity(): string
    {
        return $this->recommendedHumidity;
    }

    /**
     * Get the value of recommendationMessage
     */
    public function getRecommendationMessage(): RecommendedHumidityMessageEnum
    {
        return $this->recommendationMessage;
    }
}

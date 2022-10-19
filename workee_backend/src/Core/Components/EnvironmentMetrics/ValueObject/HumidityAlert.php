<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject;

use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedHumidityMessageEnum;

final class HumidityAlert
{
    public function __construct(
        private AlertLevelEnum $alertLevel,
        private RecommendedHumidityMessageEnum $recommendationMessage,
        private string $recommendedValue = '40% - 60%',
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
    public function getRecommendedValue(): string
    {
        return $this->recommendedValue;
    }

    /**
     * Get the value of recommendationMessage
     */
    public function getRecommendationMessage(): RecommendedHumidityMessageEnum
    {
        return $this->recommendationMessage;
    }
}

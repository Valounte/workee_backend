<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject;

use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedSoundMessageEnum;

final class SoundAlert
{
    public function __construct(
        private AlertLevelEnum $alertLevel,
        private RecommendedSoundMessageEnum $recommendationMessage,
        private string $recommendedValue = ' < 80dB',
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
    public function getRecommendationMessage(): RecommendedSoundMessageEnum
    {
        return $this->recommendationMessage;
    }
}

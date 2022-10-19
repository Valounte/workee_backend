<?php

namespace App\Core\Components\EnvironmentMetrics\ValueObject;

use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedLuminosityMessageEnum;

final class LuminosityAlert
{
    public function __construct(
        private AlertLevelEnum $alertLevel,
        private RecommendedLuminosityMessageEnum $recommendationMessage,
        private string $recommendedValue = '200lx - 500lx',
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
     * Get the value of recommendedLuminosity
     */
    public function getRecommendedValue(): string
    {
        return $this->recommendedValue;
    }

    /**
     * Get the value of recommendationMessage
     */
    public function getRecommendationMessage(): RecommendedLuminosityMessageEnum
    {
        return $this->recommendationMessage;
    }
}

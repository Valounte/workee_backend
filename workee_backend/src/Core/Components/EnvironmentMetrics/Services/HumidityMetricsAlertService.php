<?php

namespace App\Core\Components\EnvironmentMetrics\Services;

use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;
use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use App\Core\Components\EnvironmentMetrics\ValueObject\HumidityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\TemperatureAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedHumidityMessageEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureMessageEnum;

final class HumidityMetricsAlertService
{
    private AlertLevelEnum $alertLevel;

    private RecommendedHumidityMessageEnum $recommendationMessage;

    public function createAlert(HumidityMetric $humidity): HumidityAlert
    {
        $this->getData($humidity->getValue());

        return new HumidityAlert(
            $this->alertLevel,
            $this->recommendationMessage,
        );
    }

    private function getData(float $humidity): void
    {
        if ($humidity >= 40 && $humidity <= 60) {
            $this->recommendationMessage = RecommendedHumidityMessageEnum::CONFORM_HUMIDITY;
            $this->alertLevel = AlertLevelEnum::CONFORM_VALUE;
        } elseif ($humidity > 30 && $humidity < 40 || $humidity > 60 && $humidity <= 70) {
            $this->alertLevel = AlertLevelEnum::WARNING_VALUE;
            if ($humidity < 40) {
                $this->recommendationMessage = RecommendedHumidityMessageEnum::WARNING_HUMIDITY_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedHumidityMessageEnum::WARNING_HUMIDITY_TOO_HIGH;
            }
        } elseif ($humidity < 30 || $humidity > 70) {
            $this->alertLevel = AlertLevelEnum::ALERT_VALUE;
            if ($humidity < 30) {
                $this->recommendationMessage = RecommendedHumidityMessageEnum::ALERT_HUMIDITY_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedHumidityMessageEnum::ALERT_HUMIDITY_TOO_HIGH;
            }
        }
    }
}

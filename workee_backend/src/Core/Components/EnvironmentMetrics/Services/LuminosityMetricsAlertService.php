<?php

namespace App\Core\Components\EnvironmentMetrics\Services;

use App\Core\Components\EnvironmentMetrics\Entity\LuminosityMetric;
use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use App\Core\Components\EnvironmentMetrics\ValueObject\LuminosityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\TemperatureAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedLuminosityMessageEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureMessageEnum;

final class LuminosityMetricsAlertService
{
    private AlertLevelEnum $alertLevel;

    private RecommendedLuminosityMessageEnum $recommendationMessage;

    public function createAlert(LuminosityMetric $luminosity): LuminosityAlert
    {
        $this->getData($luminosity->getValue());

        return new LuminosityAlert(
            $this->alertLevel,
            $this->recommendationMessage,
        );
    }

    private function getData(float $luminosity): void
    {
        if ($luminosity >= 200 && $luminosity <= 500) {
            $this->recommendationMessage = RecommendedLuminosityMessageEnum::CONFORM_LUMINOSITY;
            $this->alertLevel = AlertLevelEnum::CONFORM_VALUE;
        } elseif ($luminosity > 100 && $luminosity < 200 || $luminosity > 500 && $luminosity <= 600) {
            $this->alertLevel = AlertLevelEnum::WARNING_VALUE;
            if ($luminosity < 200) {
                $this->recommendationMessage = RecommendedLuminosityMessageEnum::WARNING_LUMINOSITY_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedLuminosityMessageEnum::WARNING_LUMINOSITY_TOO_HIGH;
            }
        } elseif ($luminosity < 100 || $luminosity > 600) {
            $this->alertLevel = AlertLevelEnum::ALERT_VALUE;
            if ($luminosity < 100) {
                $this->recommendationMessage = RecommendedLuminosityMessageEnum::ALERT_LUMINOSITY_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedLuminosityMessageEnum::ALERT_LUMINOSITY_TOO_HIGH;
            }
        }
    }
}

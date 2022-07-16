<?php

namespace App\Core\Components\EnvironmentMetrics\Services;

use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;
use App\Core\Components\EnvironmentMetrics\Entity\SoundMetric;
use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use App\Core\Components\EnvironmentMetrics\ValueObject\HumidityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\TemperatureAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedHumidityMessageEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedSoundMessageEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureMessageEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\SoundAlert;

final class SoundMetricsAlertService
{
    private AlertLevelEnum $alertLevel;

    private RecommendedSoundMessageEnum $recommendationMessage;

    public function createAlert(SoundMetric $sound): SoundAlert
    {
        $this->getData($sound->getValue());

        return new SoundAlert(
            $this->alertLevel,
            $this->recommendationMessage,
        );
    }

    private function getData(float $sound): void
    {
        if ($sound <= 80) {
            $this->recommendationMessage = RecommendedSoundMessageEnum::CONFORM_SOUND;
            $this->alertLevel = AlertLevelEnum::CONFORM_VALUE;
        } elseif ($sound > 80 && $sound < 85) {
            $this->alertLevel = AlertLevelEnum::WARNING_VALUE;
            $this->recommendationMessage = RecommendedSoundMessageEnum::WARNING_SOUND_TOO_HIGH;
        } elseif ($sound >= 85) {
            $this->alertLevel = AlertLevelEnum::ALERT_VALUE;
            $this->recommendationMessage = RecommendedSoundMessageEnum::ALERT_SOUND_TOO_HIGH;
        }
    }
}

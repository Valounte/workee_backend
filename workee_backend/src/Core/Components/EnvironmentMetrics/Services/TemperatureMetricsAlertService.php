<?php

namespace App\Core\Components\EnvironmentMetrics\Services;

use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use App\Core\Components\EnvironmentMetrics\ValueObject\TemperatureAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureEnum;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\RecommendedTemperatureMessageEnum;

final class TemperatureMetricsAlertService
{
    private AlertLevelEnum $alertLevel;

    private RecommendedTemperatureMessageEnum $recommendationMessage;

    private RecommendedTemperatureEnum $recommendedTemperature;

    private const SUMMER = 'summer';

    private const WINTER = 'winter';

    private const AUTUMN = 'autumn';

    private const SPRING = 'spring';


    public function createAlert(TemperatureMetric $temperature): TemperatureAlert
    {
        $this->getData($temperature->getValue());

        return new TemperatureAlert(
            $this->alertLevel,
            $this->recommendationMessage,
            $this->recommendedTemperature
        );
    }

    private function getData(float $temperatureValue): void
    {
        $season = $this->getSeason();

        if ($season === self::SUMMER || $season === self::SPRING) {
            $this->getRecommendationForSpringAndSummer($temperatureValue);
        } elseif ($season === self::AUTUMN || $season === self::WINTER) {
            $this->getRecommendationForWinterAndAutumn($temperatureValue);
        }
    }

    private function getRecommendationForSpringAndSummer(float $temperatureValue): void
    {
        $this->recommendedTemperature = RecommendedTemperatureEnum::RECOMMENDED_FOR_SUMMER_AND_SPRING;
        if ($temperatureValue >= 23 && $temperatureValue <= 26) {
            $this->recommendationMessage = RecommendedTemperatureMessageEnum::CONFORM_TEMPERATURE;
            $this->alertLevel = AlertLevelEnum::CONFORM_VALUE;
        } elseif ($temperatureValue >= 21 && $temperatureValue < 23 || $temperatureValue > 26 && $temperatureValue <= 28) {
            $this->alertLevel = AlertLevelEnum::WARNING_VALUE;
            if ($temperatureValue < 23) {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::WARNING_TEMPERATURE_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::WARNING_TEMPERATURE_TOO_HIGH;
            }
        } elseif ($temperatureValue < 21 || $temperatureValue > 28) {
            $this->alertLevel = AlertLevelEnum::ALERT_VALUE;
            if ($temperatureValue < 21) {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::ALERT_TEMPERATURE_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::ALERT_TEMPERATURE_TOO_HIGH;
            }
        }
    }

    private function getRecommendationForWinterAndAutumn(float $temperatureValue): void
    {
        $this->recommendedTemperature = RecommendedTemperatureEnum::RECOMMENDED_FOR_WINTER_AND_AUTUMN;
        if ($temperatureValue >= 20 && $temperatureValue <= 23.5) {
            $this->recommendationMessage = RecommendedTemperatureMessageEnum::CONFORM_TEMPERATURE;
            $this->alertLevel = AlertLevelEnum::CONFORM_VALUE;
        } elseif ($temperatureValue >= 18 && $temperatureValue < 20 || $temperatureValue > 23.5 && $temperatureValue <= 25) {
            $this->alertLevel = AlertLevelEnum::WARNING_VALUE;
            if ($temperatureValue < 20) {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::WARNING_TEMPERATURE_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::WARNING_TEMPERATURE_TOO_HIGH;
            }
        } elseif ($temperatureValue < 18 || $temperatureValue > 25) {
            $this->alertLevel = AlertLevelEnum::ALERT_VALUE;
            if ($temperatureValue < 18) {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::ALERT_TEMPERATURE_TOO_LOW;
            } else {
                $this->recommendationMessage = RecommendedTemperatureMessageEnum::ALERT_TEMPERATURE_TOO_HIGH;
            }
        }
    }

    private function getSeason()
    {
        $month = date('n');
        $season = '';

        if ($month >= 3 && $month <= 5) {
            $season = self::SPRING;
        } elseif ($month >= 6 && $month <= 8) {
            $season = self::SUMMER;
        } elseif ($month >= 9 && $month <= 11) {
            $season = self::AUTUMN;
        } elseif ($month == 12 || $month <= 2) {
            $season = self::WINTER;
        }

        return $season;
    }
}

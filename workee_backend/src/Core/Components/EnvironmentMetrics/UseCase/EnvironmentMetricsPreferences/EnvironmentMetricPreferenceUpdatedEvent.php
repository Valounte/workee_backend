<?php

namespace App\Core\Components\EnvironmentMetrics\UseCase\EnvironmentMetricsPreferences;

final class EnvironmentMetricPreferenceUpdatedEvent
{
    public function __construct(
        private int $userId,
        private string $metricType,
        private bool $isDesactivated,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getMetricType(): string
    {
        return $this->metricType;
    }

    public function getIsDesactivated(): bool
    {
        return $this->isDesactivated;
    }
}

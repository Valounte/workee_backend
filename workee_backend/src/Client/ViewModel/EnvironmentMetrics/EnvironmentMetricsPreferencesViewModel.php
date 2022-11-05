<?php

namespace App\Client\ViewModel\EnvironmentMetrics;

final class EnvironmentMetricsPreferencesViewModel
{
    public function __construct(
        private int $id,
        private string $metricType,
        private bool $isDesactivated,
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of isDesactivated
     */
    public function getIsDesactivated()
    {
        return $this->isDesactivated;
    }

    /**
     * Get the value of metricType
     */
    public function getMetricType()
    {
        return $this->metricType;
    }
}

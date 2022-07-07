<?php

namespace App\Client\ViewModel\EnvironmentMetrics;

final class TemperatureMetricViewModel
{
    public function __construct(
        public int $id,
        public float $value,
        public int $userId,
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of value
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Get the value of user
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}

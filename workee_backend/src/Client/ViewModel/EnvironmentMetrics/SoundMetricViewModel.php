<?php

namespace App\Client\ViewModel\EnvironmentMetrics;

use App\Core\Components\EnvironmentMetrics\ValueObject\SoundAlert;

final class SoundMetricViewModel
{
    public function __construct(
        private int $id,
        private float $value,
        private int $userId,
        private string $createdAt,
        private ?SoundAlert $alert = null,
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

    /**
     * Get the value of humidityAlert
     */
    public function getAlert(): ?SoundAlert
    {
        return $this->alert;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

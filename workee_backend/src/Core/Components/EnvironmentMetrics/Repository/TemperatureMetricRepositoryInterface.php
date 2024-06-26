<?php

namespace App\Core\Components\EnvironmentMetrics\Repository;

use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;

interface TemperatureMetricRepositoryInterface
{
    public function add(TemperatureMetric $entity, bool $flush = true): void;
    public function remove(TemperatureMetric $entity, bool $flush = true): void;
    public function findOneById($id): ?TemperatureMetric;
    public function findLastTemperatureMetricByUser($user): ?TemperatureMetric;
    public function findTemperatureHistoric($user): ?array;
}

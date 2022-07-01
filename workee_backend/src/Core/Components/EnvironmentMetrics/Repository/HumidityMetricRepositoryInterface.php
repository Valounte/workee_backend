<?php

namespace App\Core\Components\EnvironmentMetrics\Repository;

use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;

interface HumidityMetricRepositoryInterface
{
    public function add(HumidityMetric $entity, bool $flush = true): void;
    public function remove(HumidityMetric $entity, bool $flush = true): void;
    public function findOneById($id): ?HumidityMetric;
    public function findLastHumidityMetricByUser($user): ?HumidityMetric;
}

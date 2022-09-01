<?php

namespace App\Core\Components\EnvironmentMetrics\Repository;

use App\Core\Components\EnvironmentMetrics\Entity\LuminosityMetric;

interface LuminosityMetricRepositoryInterface
{
    public function add(LuminosityMetric $entity, bool $flush = true): void;
    public function remove(LuminosityMetric $entity, bool $flush = true): void;
    public function findOneById($id): ?LuminosityMetric;
    public function findLastLuminosityMetricByUser($user): ?LuminosityMetric;
}

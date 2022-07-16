<?php

namespace App\Core\Components\EnvironmentMetrics\Repository;

use App\Core\Components\EnvironmentMetrics\Entity\SoundMetric;

interface SoundMetricRepositoryInterface
{
    public function add(SoundMetric $entity, bool $flush = true): void;
    public function remove(SoundMetric $entity, bool $flush = true): void;
    public function findOneById($id): ?SoundMetric;
    public function findLastSoundMetricByUser($user): ?SoundMetric;
}

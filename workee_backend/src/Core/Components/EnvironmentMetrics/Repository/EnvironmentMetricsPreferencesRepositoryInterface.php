<?php

namespace App\Core\Components\EnvironmentMetrics\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\Entity\EnvironmentMetricsPreferences;

interface EnvironmentMetricsPreferencesRepositoryInterface
{
    public function add(EnvironmentMetricsPreferences $entity, bool $flush = true): void;
    public function remove(EnvironmentMetricsPreferences $entity, bool $flush = true): void;
    public function findOneById($id): ?EnvironmentMetricsPreferences;
    public function findAll();
    public function getAllEnvironmentMetricsPreferences(User $user): array;
    public function getOneByUserAndMetricType(User $user, string $metricType): EnvironmentMetricsPreferences;
}

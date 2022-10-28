<?php

namespace App\Core\Components\Notification\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Notification\Entity\NotificationPreferences;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;

interface NotificationPreferencesRepositoryInterface
{
    public function add(NotificationPreferences $entity, bool $flush = true): void;
    public function remove(NotificationPreferences $entity, bool $flush = true): void;
    public function findOneById($id): ?NotificationPreferences;
    public function findAll();
    public function getAllNotificationsPreferences(User $user): array;
    public function getOneByUserAndAlertLevel(User $user, NotificationAlertLevelEnum $alertLevel): NotificationPreferences;
}

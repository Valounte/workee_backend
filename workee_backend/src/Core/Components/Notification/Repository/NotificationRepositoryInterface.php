<?php

namespace App\Core\Components\Notification\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Notification\Entity\Notification;

interface NotificationRepositoryInterface
{
    public function add(Notification $entity, bool $flush = true): void;
    public function remove(Notification $entity, bool $flush = true): void;
    public function findOneById($id): ?Notification;
    public function findAll();
    public function findLastNotifications(User $receiver): ?array;
}

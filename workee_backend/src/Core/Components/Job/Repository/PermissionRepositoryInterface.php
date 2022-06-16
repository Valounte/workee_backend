<?php

namespace App\Core\Components\Job\Repository;

use App\Core\Components\Job\Entity\Permission;

interface PermissionRepositoryInterface
{
    public function add(Permission $entity, bool $flush = true): void;
    public function remove(Permission $entity, bool $flush = true): void;
    public function findOneById($id): ?Permission;
}

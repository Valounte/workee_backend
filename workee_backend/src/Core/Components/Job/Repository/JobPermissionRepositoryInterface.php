<?php

namespace App\Core\Components\Job\Repository;

use App\Core\Components\Job\Entity\Job;
use App\Core\Components\Job\Entity\JobPermission;

interface JobPermissionRepositoryInterface
{
    public function add(JobPermission $entity, bool $flush = true): void;
    public function remove(JobPermission $entity, bool $flush = true): void;
    public function findOneById($id): ?JobPermission;
    public function findPermissionsByJob($job): ?array;
}

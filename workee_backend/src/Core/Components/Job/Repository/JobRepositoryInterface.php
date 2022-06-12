<?php

namespace App\Core\Components\Job\Repository;

use App\Core\Components\Job\Entity\Job;

interface JobRepositoryInterface
{
    public function add(Job $entity, bool $flush = true): void;
    public function remove(Job $entity, bool $flush = true): void;
    public function findOneById($id): ?Job;
}

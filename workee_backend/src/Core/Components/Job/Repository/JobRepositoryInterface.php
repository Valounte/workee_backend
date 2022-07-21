<?php

namespace App\Core\Components\Job\Repository;

use App\Core\Components\Job\Entity\Job;
use App\Core\Components\Company\Entity\Company;

interface JobRepositoryInterface
{
    public function add(Job $entity, bool $flush = true): void;
    public function remove(Job $entity, bool $flush = true): void;
    public function findOneById($id): ?Job;
    public function findByCompany(Company $company): ?array;
}

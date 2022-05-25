<?php

namespace App\Core\Components\Company\Repository;

use App\Core\Components\Company\Entity\Company;

interface CompanyRepositoryInterface
{
    public function add(Company $entity, bool $flush = true): void;
    public function remove(Company $entity, bool $flush = true): void;
    public function findOneById($id): ?Company;
}

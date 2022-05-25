<?php

namespace App\Core\Components\Team\Repository;

use App\Core\Components\Team\Entity\Team;

interface TeamRepositoryInterface
{
    public function add(Team $entity, bool $flush = true): void;
    public function remove(Team $entity, bool $flush = true): void;
    public function findOneById($id): ?Team;
    public function findAll();
}

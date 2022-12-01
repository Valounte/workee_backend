<?php

namespace App\Core\Components\Logs\Repository;

use App\Core\Components\Logs\Entity\Logs;

interface LogsRepositoryInterface
{
    public function add(Logs $entity, bool $flush = true): void;
    public function remove(Logs $entity, bool $flush = true): void;
}

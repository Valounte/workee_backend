<?php

namespace App\Core\Components\User\Repository;

use App\Core\Components\User\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $entity, bool $flush = true): void;
    public function remove(User $entity, bool $flush = true): void;
    public function findByTeamId(int $team): ?array;
    public function findUserByEmail(string $email): ?User;
    public function findUserById(int $id): ?User;
    public function findByCompany(int $company): ?array;
}

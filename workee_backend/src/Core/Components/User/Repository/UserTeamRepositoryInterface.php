<?php

namespace App\Core\Components\User\Repository;

use App\Core\Components\User\Entity\UserTeam;

interface UserTeamRepositoryInterface
{
    public function add(UserTeam $entity, bool $flush = true): void;
    public function remove(UserTeam $entity, bool $flush = true): void;
    public function findOneById($id): ?UserTeam;
    public function findUsersByTeamId($team): ?array;
    public function findTeamsByUser($user): ?array;
}

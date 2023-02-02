<?php

namespace App\Core\Components\ProfessionalDevelopment\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;

interface ProfessionalDevelopmentGoalRepositoryInterface
{
    public function add(ProfessionalDevelopmentGoal $entity, bool $flush = true): void;
    public function remove(ProfessionalDevelopmentGoal $entity, bool $flush = true): void;
    public function get(int $id): ProfessionalDevelopmentGoal;
    public function getByUser(User $user): array;
}

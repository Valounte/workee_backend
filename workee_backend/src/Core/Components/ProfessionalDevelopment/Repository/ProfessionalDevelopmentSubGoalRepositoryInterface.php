<?php

namespace App\Core\Components\ProfessionalDevelopment\Repository;

use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentSubGoal;

interface ProfessionalDevelopmentSubGoalRepositoryInterface
{
    public function add(ProfessionalDevelopmentSubGoal $entity, bool $flush = true): void;
    public function remove(ProfessionalDevelopmentSubGoal $entity, bool $flush = true): void;
    public function getSubGoalsByGoal(ProfessionalDevelopmentGoal $goal): array;
    public function get(int $id): ProfessionalDevelopmentSubGoal;
    public function getSubGoalsViewModelsByGoal(ProfessionalDevelopmentGoal $goal): array;
}

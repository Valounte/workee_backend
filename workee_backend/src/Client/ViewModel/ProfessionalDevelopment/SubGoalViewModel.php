<?php

namespace App\Client\ViewModel\ProfessionalDevelopment;

use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;

final class SubGoalViewModel
{
    public function __construct(
        private int $id,
        private string $subGoal,
        private GoalStatusEnum $status,
    ) {
    }

    /**
     * Get the value of subGoal
     */
    public function getSubGoal(): string
    {
        return $this->subGoal;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): GoalStatusEnum
    {
        return $this->status;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }
}

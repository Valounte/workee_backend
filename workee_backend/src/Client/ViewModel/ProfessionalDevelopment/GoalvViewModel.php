<?php

namespace App\Client\ViewModel\ProfessionalDevelopment;

final class GoalViewModel
{
    public function __construct(
        private int $id,
        private string $goal,
        private array $subGoals,
    ) {
    }

    /**
     * Get the value of goal
     */
    public function getGoal(): string
    {
        return $this->goal;
    }

    /**
     * Get the value of subGoals
     */
    public function getSubGoals(): array
    {
        return $this->subGoals;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }
}

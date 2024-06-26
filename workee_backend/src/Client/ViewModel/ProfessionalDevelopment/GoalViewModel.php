<?php

namespace App\Client\ViewModel\ProfessionalDevelopment;

use App\Client\ViewModel\ProfessionalDevelopment\ProfessionalDevelopmentUserViewModel;
use DateTime;

final class GoalViewModel
{
    public function __construct(
        private int $id,
        private string $goal,
        private int $progression,
        private DateTime $startDate,
        private DateTime $endDate,
        private ?array $subGoals = null,
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
    public function getSubGoals(): ?array
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

    /**
     * Get the value of progression
     */
    public function getProgression()
    {
        return $this->progression;
    }

    /**
     * Get the value of startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}

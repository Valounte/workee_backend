<?php

namespace App\Core\Components\ProfessionalDevelopment\UseCase;

final class SubGoalHasBeenUpdatedEvent
{
    public function __construct(
        private int $updatedSubGoalId,
        private int $relatedGoalId,
    ) {
    }

    /**
     * Get the value of updatedSubGoalId
     */
    public function getUpdatedSubGoalId(): int
    {
        return $this->updatedSubGoalId;
    }

    /**
     * Get the value of relatedGoalId
     */
    public function getRelatedGoalId(): int
    {
        return $this->relatedGoalId;
    }
}

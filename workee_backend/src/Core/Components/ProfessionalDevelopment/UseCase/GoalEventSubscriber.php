<?php

namespace App\Core\Components\ProfessionalDevelopment\UseCase;

use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Core\Components\ProfessionalDevelopment\UseCase\SubGoalHasBeenUpdatedEvent;

final class GoalEventSubscriber implements AsMessageHandler
{
    public function __construct(
        private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository,
    ) {
    }

    public function updateGoalProgression(SubGoalHasBeenUpdatedEvent $event): void
    {
        $goal = $this->professionalDevelopmentGoalRepository->get($event->getRelatedGoalId());
    }
}

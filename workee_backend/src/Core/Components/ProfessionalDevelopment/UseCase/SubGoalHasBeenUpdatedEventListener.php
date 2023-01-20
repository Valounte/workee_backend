<?php

namespace App\Core\Components\ProfessionalDevelopment\UseCase;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\ProfessionalDevelopment\UseCase\SubGoalHasBeenUpdatedEvent;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepositoryInterface;

final class SubGoalHasBeenUpdatedEventListener implements MessageHandlerInterface
{
    public function __construct(
        private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository,
        private ProfessionalDevelopmentSubGoalRepositoryInterface $professionalDevelopmentSubGoalRepository,
    ) {
    }

    public function __invoke(SubGoalHasBeenUpdatedEvent $event): void
    {
        $goal = $this->professionalDevelopmentGoalRepository->get($event->getRelatedGoalId());
        $subGoals = $this->professionalDevelopmentSubGoalRepository->getSubGoalsByGoal($goal);

        $doneSubgoalsCount = 0;

        foreach ($subGoals as $subGoal) {
            if ($subGoal->isDone()) {
                $doneSubgoalsCount++;
            }
        }

        $progression = (int) round(($doneSubgoalsCount * 100) / count($subGoals));

        $goal->setProgression($progression);
        $this->professionalDevelopmentGoalRepository->add($goal);
    }
}

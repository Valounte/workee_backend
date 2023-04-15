<?php

namespace App\Tests\Unit\Core\Components\ProfessionalDevelopment\UseCase;

use DateTime;
use App\Tests\Unit\StubUserFactory;
use App\Tests\Unit\AbstractTestCase;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\UseCase\SubGoalHasBeenUpdatedEvent;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentSubGoal;
use App\Core\Components\ProfessionalDevelopment\UseCase\SubGoalHasBeenUpdatedEventListener;
use App\Infrastructure\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepository;
use App\Infrastructure\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepository;

final class SubGoalHasBeenUpdatedEventListenerTest extends AbstractTestCase
{
    private SubGoalHasBeenUpdatedEventListener $listener;

    private ProfessionalDevelopmentGoalRepository $professionalDevelopmentGoalRepository;

    private ProfessionalDevelopmentSubGoalRepository $professionalDevelopmentSubGoalRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->professionalDevelopmentGoalRepository = $this->createMock(ProfessionalDevelopmentGoalRepository::class);
        $this->professionalDevelopmentSubGoalRepository = $this->createMock(ProfessionalDevelopmentSubGoalRepository::class);
        $this->listener = new SubGoalHasBeenUpdatedEventListener(
            $this->professionalDevelopmentGoalRepository,
            $this->professionalDevelopmentSubGoalRepository,
        );
    }

    public function test_sub_goal_has_been_updated_event_listener(): void
    {
        $event = new SubGoalHasBeenUpdatedEvent(1, 1);
        $goal = new ProfessionalDevelopmentGoal(StubUserFactory::create(1), 'test', new DateTime(), new DateTime());

        $this->professionalDevelopmentGoalRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn($goal);

        $this->professionalDevelopmentSubGoalRepository
            ->expects($this->once())
            ->method('getSubGoalsByGoal')
            ->willReturn([
                new ProfessionalDevelopmentSubGoal('test', GoalStatusEnum::DONE, $goal),
                new ProfessionalDevelopmentSubGoal('test', GoalStatusEnum::IN_PROGRESS, $goal),
            ]);

        $goal->setProgression(50);

        $this->professionalDevelopmentGoalRepository
            ->expects($this->once())
            ->method('add')
            ->with($goal);

        $this->listener->__invoke($event);
    }
}

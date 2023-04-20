<?php

namespace App\Tests\Unit\Core\Components\TeaOrCoffeeMeeting\UseCase;

use DateTime;
use App\Tests\Unit\StubUser;
use App\Tests\Unit\AbstractTestCase;
use App\Core\Components\Company\Entity\Company;
use App\Core\Components\Team\Entity\Team;
use App\Infrastructure\User\Repository\UserTeamRepository;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepository;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepository;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateRandomInTeamTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateRandomInTeamTeaOrCoffeeMeetingHandler;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeamTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeamTeaOrCoffeeMeetingHandler;
use App\Tests\Unit\StubUserFactory;

final class CreateTeamTeaOrCoffeeMeetingHandlerTest extends AbstractTestCase
{
    private TeaOrCoffeeMeetingRepository $teaOrCoffeeMeetingRepository;

    private TeaOrCoffeeMeetingUserRepository $teaOrCoffeeMeetingUserRepository;

    private UserTeamRepository $userTeamRepository;

    private CreateTeamTeaOrCoffeeMeetingHandler $handler;

    protected function setUp(): void
    {
        $this->teaOrCoffeeMeetingRepository = $this->createMock(TeaOrCoffeeMeetingRepository::class);
        $this->teaOrCoffeeMeetingUserRepository = $this->createMock(TeaOrCoffeeMeetingUserRepository::class);
        $this->userTeamRepository = $this->createMock(UserTeamRepository::class);

        $this->handler = new CreateTeamTeaOrCoffeeMeetingHandler(
            $this->teaOrCoffeeMeetingRepository,
            $this->userTeamRepository,
            $this->teaOrCoffeeMeetingUserRepository,
        );

        parent::setUp();
    }
    public function test_create_tea_or_coffee_meeting(): void
    {
        $initiator = StubUserFactory::create(2);
        $invitedUser = StubUserFactory::create(1, 'test2@gmail.com', 'test2', 'test2', new Company('test2'));
        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting($initiator, new DateTime('2021-01-01'), TeaOrCoffeeMeetingTypeEnum::TEAM, 'test');

        $event = new CreateTeamTeaOrCoffeeMeetingCommand(
            $initiator,
            new Team('test', 'test', new Company('test'), new DateTime()),
            new DateTime('2021-01-01'),
            "test",
        );

        $this->userTeamRepository
            ->expects($this->once())
            ->method('findUsersByTeamId')
            ->willReturn([$invitedUser]);

        $this->teaOrCoffeeMeetingRepository
            ->expects($this->once())
            ->method('add')
            ->with($teaOrCoffeeMeeting);

        $this->teaOrCoffeeMeetingUserRepository->expects($this->once())
            ->method('add')
            ->with(new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $invitedUser));

        $this->handler->__invoke($event);
    }
}

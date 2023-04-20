<?php

namespace App\Tests\Unit\Core\Components\TeaOrCoffeeMeeting\UseCase;

use DateTime;
use App\Tests\Unit\StubUser;
use App\Tests\Unit\AbstractTestCase;
use App\Core\Components\Company\Entity\Company;
use App\Infrastructure\User\Repository\UserRepository;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepository;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeaOrCoffeeMeetingHandler;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepository;
use App\Tests\Unit\StubUserFactory;

final class CreateTeaOrCoffeeMeetingHandlerTest extends AbstractTestCase
{
    private TeaOrCoffeeMeetingRepository $teaOrCoffeeMeetingRepository;

    private UserRepository $userRepository;

    private TeaOrCoffeeMeetingUserRepository $teaOrCoffeeMeetingUserRepository;

    private CreateTeaOrCoffeeMeetingHandler $handler;

    protected function setUp(): void
    {
        $this->teaOrCoffeeMeetingRepository = $this->createMock(TeaOrCoffeeMeetingRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->teaOrCoffeeMeetingUserRepository = $this->createMock(TeaOrCoffeeMeetingUserRepository::class);

        $this->handler = new CreateTeaOrCoffeeMeetingHandler(
            $this->teaOrCoffeeMeetingRepository,
            $this->userRepository,
            $this->teaOrCoffeeMeetingUserRepository,
        );

        parent::setUp();
    }
    public function test_create_tea_or_coffee_meeting(): void
    {
        $initiator = StubUserFactory::create(2);
        $invitedUser = StubUserFactory::create(1, 'test2@gmail.com', 'test2', 'test2', new Company('test2'));
        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting($initiator, new DateTime('2021-01-01'), TeaOrCoffeeMeetingTypeEnum::CLASSIC, 'test');

        $event = new CreateTeaOrCoffeeMeetingCommand(
            $initiator,
            [1],
            new DateTime('2021-01-01'),
            "test",
        );

        $this->userRepository->expects($this->once())
            ->method('findUserById')
            ->willReturn($invitedUser);

        $teaOrCoffeeMeetingMock = $this->getMockBuilder(TeaOrCoffeeMeeting::class)
            ->disableOriginalConstructor()
            ->getMock();
        $teaOrCoffeeMeetingMock->method('getId')
            ->willReturn(1234);

        $this->teaOrCoffeeMeetingRepository->expects($this->once())
            ->method('add')
            ->with($teaOrCoffeeMeeting);

        $this->teaOrCoffeeMeetingUserRepository->expects($this->once())
            ->method('add')
            ->with(new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $invitedUser));

        $this->handler->__invoke($event);
    }
}

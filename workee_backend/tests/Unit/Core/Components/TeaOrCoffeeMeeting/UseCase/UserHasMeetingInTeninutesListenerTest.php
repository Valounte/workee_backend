<?php

namespace App\Tests\Unit\Core\Components\TeaOrCoffeeMeeting\UseCase;

use App\Tests\Unit\StubUserFactory;
use App\Tests\Unit\AbstractTestCase;
use Symfony\Component\Mercure\Update;
use App\Core\Components\User\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\User\Repository\UserRepository;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\UserHasMeetingInTenMinutesEvent;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\UserHasMeetingInTenMinutesListener;

final class UserHasMeetingInTeninutesListenerTest extends AbstractTestCase
{
    private TokenService $tokenService;

    private UserRepository $userRepository;

    private HubInterface $hub;

    private UserHasMeetingInTenMinutesListener $listener;

    protected function setUp(): void
    {
        $this->tokenService = new TokenService();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->hub = $this->createMock(HubInterface::class);

        $this->listener = new UserHasMeetingInTenMinutesListener(
            $this->tokenService,
            $this->userRepository,
            $this->hub,
            'mercure_hub_url'
        );

        parent::setUp();
    }

    public function test_user_has_meeting_in_ten_minutes(): void
    {
        $meetingName = 'meeting_name';
        $event = new UserHasMeetingInTenMinutesEvent(
            1,
            [2, 3, 4],
            $meetingName,
        );

        $this->userRepository
            ->expects($this->exactly(4))
            ->method('findUserById')
            ->withConsecutive([2], [3], [4], [1])
            ->willReturn(
                StubUserFactory::create(1, 'user1'),
                StubUserFactory::create(2, 'user2'),
                StubUserFactory::create(3, 'user3'),
                StubUserFactory::create(4, 'user4'),
            );

        $this->hub
            ->expects($this->exactly(4))
            ->method('publish')
            ->withConsecutive(
                [$this->createHubNotification(StubUserFactory::create(1, 'user1'), $meetingName)],
                [$this->createHubNotification(StubUserFactory::create(2, 'user2'), $meetingName)],
                [$this->createHubNotification(StubUserFactory::create(3, 'user3'), $meetingName)],
                [$this->createHubNotification(StubUserFactory::create(4, 'user4'), $meetingName)],
            );


        $this->listener->__invoke($event);
    }

    private function createHubNotification(User $user, string $name): Update
    {
        $jwt = $this->tokenService->createLoginToken($user);

        $update = new Update(
            'mercure_hub_url' . '/teaOrCoffee' . '/' . $jwt,
            json_encode([
                'type' => "TeaOrCoffee",
                'message' => "has a teaOrCoffee meeting in 10 minutes",
                'name' => $name,
            ])
        );

        return $update;
    }
}

<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\UserHasMeetingInTenMinutesEvent;

final class UserHasMeetingInTenMinutesListener implements MessageHandlerInterface
{
    public function __construct(
        private TokenService $tokenService,
        private UserRepositoryInterface $userRepository,
        private HubInterface $hub,
        private string $mercureHubUrl,
    ) {
    }

    public function __invoke(UserHasMeetingInTenMinutesEvent $event): void
    {
        $name = $event->meetingName;
        $usersIds = $event->invitedUsersIds;
        $usersIds[] = $event->initiatorId;

        foreach ($usersIds as $userId) {
            $user = $this->userRepository->findUserById($userId);
            $jwt = $this->tokenService->createLoginToken($user);

            $update = new Update(
                $this->mercureHubUrl . '/teaOrCoffee' . '/' . $jwt,
                json_encode([
                    'type' => "TeaOrCoffee",
                    'message' => "has a teaOrCoffee meeting in 10 minutes",
                    'name' => $name,
                ])
            );
            $this->hub->publish($update);
        }
    }
}

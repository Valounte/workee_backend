<?php

namespace App\Core\Components\Feedback\UseCase;

use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\User\Repository\UserRepository;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserHasMeetingInTenMinutesListener implements MessageHandlerInterface
{
    public function __construct(
        private TokenService $tokenService,
        private UserRepository $userRepository,
        private HubInterface $hub,
        private string $mercureHubUrl,
    ) {
    }

    public function __invoke(UserHasMeetingInTenMinutesEvent $event): void
    {
        $userId = $event->getUserId();

        $user = $this->userRepository->findUserById($userId);

        $jwt = $this->tokenService->createLoginToken($user);
        $update = new Update(
            $this->mercureHubUrl . '/teaOrCoffee' . '/' . $jwt,
            json_encode([
                'message' => "has a teaOrCoffee meeting in 10 minutes",
                'userId' => $user->getId(),
                ])
        );
        $this->hub->publish($update);
    }
}

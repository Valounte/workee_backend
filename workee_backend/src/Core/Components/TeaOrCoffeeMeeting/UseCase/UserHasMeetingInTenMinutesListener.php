<?php

namespace App\Core\Components\Feedback\UseCase;

use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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
        var_dump($event);
        $user = $this->userRepository->findUserById($event->getUserId());

        $jwt = $this->tokenService->createLoginToken($user);
        $update = new Update(
            $this->mercureHubUrl . '/teaOrCoffee' . '/' . $jwt,
            json_encode([
                'message' => "has a teaOrCoffee meeting in 10 minutes",
            ])
        );
        $this->hub->publish($update);
    }
}

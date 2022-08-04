<?php

namespace App\Core\Components\Notification\UseCase;

use App\Client\ViewModel\Notification\NotificationViewModel;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Infrastructure\Token\Services\TokenService;

final class NotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private HubInterface $hub,
        private string $mercureHubUrl,
        private TokenService $tokenService,
    ) {
    }

    public function __invoke(NotificationCommand $command): void
    {
        $notification = $command->getNotification();

        $jwt = $this->tokenService->createLoginToken($notification->getRecepteur());

        $update = new Update(
            $this->mercureHubUrl . '/notification' . '/' . $jwt,
            json_encode([
                'firstname' => $notification->getSender()->getFirstname(),
                'lastname' => $notification->getSender()->getLastname(),
                'message' => $notification->getMessage(),
                'alertLevel' => $notification->getAlertLevel(),
                'createdAt' => $notification->getCreatedAt(),
            ])
        );

        $this->hub->publish($update);
    }
}

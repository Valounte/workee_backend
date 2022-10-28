<?php

namespace App\Core\Components\Notification\UseCase;

use Symfony\Component\Mercure\Update;
use App\Core\Components\User\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\Notification\Repository\NotificationRepositoryInterface;
use App\Core\Components\Notification\Repository\NotificationPreferencesRepositoryInterface;

final class NotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private HubInterface $hub,
        private string $mercureHubUrl,
        private TokenService $tokenService,
        private NotificationRepositoryInterface $notificationRepository,
        private NotificationPreferencesRepositoryInterface $notificationPreferencesRepository,
    ) {
    }

    public function __invoke(NotificationCommand $command): void
    {
        $notification = $command->getNotification();

        $jwt = $this->tokenService->createLoginToken($notification->getReceiver());

        $this->notificationRepository->add($notification);

        if ($this->shouldSendNotification($notification->getReceiver(), $notification->getAlertLevel())) {
            $update = new Update(
                $this->mercureHubUrl . '/notification' . '/' . $jwt,
                json_encode([
                    'firstname' => $notification->getSender()->getFirstname(),
                    'lastname' => $notification->getSender()->getLastname(),
                    'message' => $notification->getMessage(),
                    'alertLevel' => $notification->getAlertLevel(),
                    'createdAt' => $notification->getCreated_at(),
                    'notificationId' => $notification->getId(),
                ])
            );

            $this->hub->publish($update);
        }
    }

    private function shouldSendNotification(User $user, NotificationAlertLevelEnum $alertLevel): bool
    {
        $notificationPreferences = $this->notificationPreferencesRepository->getOneByUserAndAlertLevel($user, $alertLevel);

        return $notificationPreferences->getIsMute() === false;
    }
}

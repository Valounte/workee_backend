<?php

namespace App\Client\Controller\Notification;

use App\Client\ViewModel\Notification\NotificationViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\Notification\Entity\Notification;
use App\Core\Components\Notification\Repository\NotificationRepositoryInterface;
use App\Core\Components\User\Service\GetUserService;
use App\Infrastructure\Response\Services\JsonResponseService;

final class NotificationController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private MessageBusInterface $messageBus,
        private UserRepositoryInterface $userRepository,
        private NotificationRepositoryInterface $notificationRepository,
        private GetUserService $getUserService,
        private JsonResponseService $jsonResponseService,
    ) {
    }

    /**
     * @Route("/api/notification", name="postHumidity", methods={"POST"})
     */
    public function publish(Request $request): Response
    {
        try {
            $sender = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);

        $alertLevel = match ($input['alertLevel']) {
            'important' => NotificationAlertLevelEnum::IMPORTANT_ALERT,
            'urgent' => NotificationAlertLevelEnum::URGENT_ALERT,
            default => NotificationAlertLevelEnum::NORMAL_ALERT,
        };

        $receiver = $this->userRepository->findUserById($input['recepteurId']);

        $command = new NotificationCommand(
            new Notification(
                $input['message'],
                $sender,
                $receiver,
                $alertLevel,
            ),
        );

        $this->messageBus->dispatch($command);

        return new Response('Notification sent !');
    }

    /**
     * @Route("/api/notification", name="getNotifications", methods={"GET"})
     */
    public function getNotifications(Request $request): Response
    {
        try {
            $receiver = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $notifications = $this->notificationRepository->findLastNotifications($receiver);
        $notificationsViewModel = [];

        foreach ($notifications as $notification) {
            $notificationsViewModel[] = new NotificationViewModel(
                $notification->getMessage(),
                $notification->getSender()->getFirstname(),
                $notification->getSender()->getLastname(),
                $notification->getAlertLevel(),
                $notification->getCreated_at(),
            );
        }

        return $this->jsonResponseService->create($notificationsViewModel);
    }
}

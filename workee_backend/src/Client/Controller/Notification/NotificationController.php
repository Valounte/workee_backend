<?php

namespace App\Client\Controller\Notification;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Service\GetUserService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Notification\Entity\Notification;
use App\Core\Components\Notification\UseCase\TestCommand;
use App\Client\ViewModel\Notification\NotificationViewModel;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\Notification\Repository\NotificationRepositoryInterface;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Infrastructure\User\Repository\UserTeamRepository;

final class NotificationController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private MessageBusInterface $messageBus,
        private UserRepositoryInterface $userRepository,
        private NotificationRepositoryInterface $notificationRepository,
        private GetUserService $getUserService,
        private JsonResponseService $jsonResponseService,
        private UserTeamRepositoryInterface $userTeamRepository,
    ) {
    }

    /**
     * @Route("/api/send-notification", name="send-notification", methods={"POST"})
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

        $usersById = [];
        if (isset($input["usersId"])) {
            foreach ($input["usersId"] as $userId) {
                $usersById[] = $this->userRepository->findUserById($userId);
            }
        }

        $usersByTeam = [];
        if (isset($input["teamsId"])) {
            foreach ($input["teamsId"] as $teamId) {
                $usersByTeam = $this->userTeamRepository->findUsersByTeamId($teamId);
            }
        }

        foreach (array_merge($usersById, $usersByTeam) as $user) {
            $command = new NotificationCommand(
                new Notification(
                    $input['message'],
                    $sender,
                    $user,
                    $alertLevel,
                ),
            );

            $this->messageBus->dispatch($command);
        }


        return $this->jsonResponseService->successJsonResponse('Notifications sent', 200);
    }

    /**
     * @Route("/api/notifications", name="getNotifications", methods={"GET"})
     */
    public function getNotifications(Request $request): Response
    {
        try {
            $receiver = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $limit = $request->query->get('limit');

        $notifications = $this->notificationRepository->findLastNotifications(
            $receiver,
            isset($limit) ? $limit : 20,
        );

        if (empty($notifications)) {
            return $this->jsonResponseService->successJsonResponse('No notifications found', 404);
        }

        $notificationsViewModel = [];

        foreach ($notifications as $notification) {
            $notificationsViewModel[] = new NotificationViewModel(
                $notification->getId(),
                $notification->getMessage(),
                $notification->getSender()->getFirstname(),
                $notification->getSender()->getLastname(),
                $notification->getAlertLevel(),
                $notification->getCreated_at(),
            );
        }

        return $this->jsonResponseService->create($notificationsViewModel);
    }

    /**
     * @Route("/api/test", name="tests", methods={"POST"})
     */
    public function test(Request $request): Response
    {
        $this->messageBus->dispatch(
            new Envelope(new TestCommand('test'), [new DelayStamp(1000)])
        );

        return $this->jsonResponseService->successJsonResponse('Notification sent', 200);
    }
}

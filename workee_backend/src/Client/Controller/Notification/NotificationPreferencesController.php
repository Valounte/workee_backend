<?php

namespace App\Client\Controller\Notification;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Logs\Services\LogsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Logs\Repository\LogsRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\Notification\Repository\NotificationPreferencesRepositoryInterface;

final class NotificationPreferencesController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private NotificationPreferencesRepositoryInterface $notificationPreferencesRepository,
        private JsonResponseService $jsonResponseService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/notification-preferences", name="notification-preferences", methods={"GET"})
     */
    public function getNotificationPreferences(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $notificationPreferences = $this->notificationPreferencesRepository->getAllNotificationsPreferences($user);

        return $this->jsonResponseService->create($notificationPreferences);
    }

    /**
     * @Route("/api/notification-preference", name="notification-preference", methods={"PUT"})
     */
    public function updateNotificationPreference(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);
        $isMute = $input['isMute'];
        $alertLevel = $input['alertLevel'];

        $alertLevelEnum = match ($alertLevel) {
            'normal' => NotificationAlertLevelEnum::NORMAL_ALERT,
            'important' => NotificationAlertLevelEnum::IMPORTANT_ALERT,
            'urgent' => NotificationAlertLevelEnum::URGENT_ALERT,
            default => NotificationAlertLevelEnum::NORMAL_ALERT,
        };

        $notificationPreference = $this->notificationPreferencesRepository->getOneByUserAndAlertLevel($user, $alertLevelEnum);

        if ($notificationPreference === null) {
            $this->logsService->add(404, LogsContextEnum::NOTIFICATION_SETTINGS, LogsAlertEnum::WARNING, null, $user);

            return new JsonResponse('Notification preference not found', 404);
        }

        $notificationPreference->setIsMute($isMute);

        $this->notificationPreferencesRepository->add($notificationPreference);
        $this->logsService->add(200, LogsContextEnum::NOTIFICATION_SETTINGS, null, null, $user);
        return $this->jsonResponseService->successJsonResponse('Notification preference updated', 200);
    }
}

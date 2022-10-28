<?php

namespace App\Client\Controller\Notification;

use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Notification\Repository\NotificationPreferencesRepositoryInterface;
use App\Infrastructure\Response\Services\JsonResponseService;

final class NotificationPreferencesController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private NotificationPreferencesRepositoryInterface $notificationPreferencesRepository,
        private JsonResponseService $jsonResponseService,
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
        $notificationPreference->setIsMute($isMute);

        $this->notificationPreferencesRepository->add($notificationPreference);

        return $this->jsonResponseService->successJsonResponse('Notification preference updated', 200);
    }
}

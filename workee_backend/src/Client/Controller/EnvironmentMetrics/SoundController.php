<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserNotFoundException;
use App\Core\Components\EnvironmentMetrics\Entity\SoundMetric;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Client\ViewModel\EnvironmentMetrics\SoundMetricViewModel;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\EnvironmentMetrics\Services\SoundMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Services\HumidityMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\SoundMetricRepositoryInterface;
use App\Core\Components\EnvironmentMetrics\Repository\HumidityMetricRepositoryInterface;

final class SoundController extends AbstractController
{
    public function __construct(
        private SoundMetricRepositoryInterface $soundMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JsonResponseService $jsonResponseService,
        private SoundMetricsAlertService $soundMetricsAlertService,
    ) {
    }

    /**
     * @Route("/api/sound", name="postSound", methods={"POST"})
     */
    public function postSound(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);

        $soundMetric = new SoundMetric(
            (float) $data["value"],
            $user,
        );

        $this->soundMetricRepository->add($soundMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_sound", name="getCurrentHumidity", methods={"GET"})
     */
    public function getCurrentHumidity(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $lastSoundValue = $this->soundMetricRepository->findLastSoundMetricByUser($user);

        if ($lastSoundValue === null) {
            return new JsonResponse("no data", 404);
        }

        $humidityViewModel = new SoundMetricViewModel(
            $lastSoundValue->getId(),
            $lastSoundValue->getValue(),
            $user->getId(),
            $this->soundMetricsAlertService->createAlert($lastSoundValue),
        );

        return $this->jsonResponseService->create($humidityViewModel, 200);
    }
}

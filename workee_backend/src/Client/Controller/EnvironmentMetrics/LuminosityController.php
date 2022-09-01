<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserNotFoundException;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\EnvironmentMetrics\Entity\LuminosityMetric;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Client\ViewModel\EnvironmentMetrics\LuminosityMetricViewModel;
use App\Core\Components\EnvironmentMetrics\ValueObject\LuminosityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\Services\LuminosityMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\LuminosityMetricRepositoryInterface;

final class LuminosityController extends AbstractController
{
    public function __construct(
        private LuminosityMetricRepositoryInterface $humidityMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JsonResponseService $jsonResponseService,
        private LuminosityMetricsAlertService $humidityMetricsAlertService,
    ) {
    }

    /**
     * @Route("/api/luminosity", name="postLuminosity", methods={"POST"})
     */
    public function postLuminosity(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);

        $humidityMetric = new LuminosityMetric(
            (float) $data["value"],
            $user,
        );

        $this->humidityMetricRepository->add($humidityMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_luminosity", name="getCurrentLuminosity", methods={"GET"})
     */
    public function getCurrentLuminosity(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $lastLuminosityValue = $this->humidityMetricRepository->findLastLuminosityMetricByUser($user);

        if ($lastLuminosityValue === null) {
            return new JsonResponse("no data", 404);
        }

        $humidityViewModel = new LuminosityMetricViewModel(
            $lastLuminosityValue->getId(),
            $lastLuminosityValue->getValue(),
            $user->getId(),
            $this->humidityMetricsAlertService->createAlert($lastLuminosityValue),
        );
        return $this->jsonResponseService->create($humidityViewModel, 200);
    }
}

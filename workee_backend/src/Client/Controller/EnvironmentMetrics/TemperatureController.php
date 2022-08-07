<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserNotFoundException;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\EnvironmentMetrics\Entity\TemperatureMetric;
use App\Client\ViewModel\EnvironmentMetrics\TemperatureMetricViewModel;
use App\Core\Components\EnvironmentMetrics\Services\TemperatureMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\TemperatureMetricRepositoryInterface;

final class TemperatureController extends AbstractController
{
    public function __construct(
        private TemperatureMetricRepositoryInterface $temperatureMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JsonResponseService $jsonResponseService,
        private TemperatureMetricsAlertService $temperatureMetricsAlertService,
    ) {
    }

    /**
     * @Route("/api/temperature", name="postTemperature", methods={"POST"})
     */
    public function postTemperature(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);

        $temperatureMetric = new TemperatureMetric(
            (float) $data["value"],
            $user,
        );

        $this->temperatureMetricRepository->add($temperatureMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_temperature", name="getCurrentTemperature", methods={"GET"})
     */
    public function getCurrentTemperature(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $lastTemperatureValue = $this->temperatureMetricRepository->findLastTemperatureMetricByUser($user);

        if ($lastTemperatureValue === null) {
            return new JsonResponse("no data", 404);
        }

        $temperatureViewModel = new TemperatureMetricViewModel(
            $lastTemperatureValue->getId(),
            $lastTemperatureValue->getValue(),
            $user->getId(),
            $this->temperatureMetricsAlertService->createAlert($lastTemperatureValue),
        );

        return $this->jsonResponseService->create($temperatureViewModel);
    }
}

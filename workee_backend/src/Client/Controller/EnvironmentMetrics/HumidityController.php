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
use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Client\ViewModel\EnvironmentMetrics\HumidityMetricViewModel;
use App\Core\Components\EnvironmentMetrics\ValueObject\HumidityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\Services\HumidityMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\HumidityMetricRepositoryInterface;

final class HumidityController extends AbstractController
{
    public function __construct(
        private HumidityMetricRepositoryInterface $humidityMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JsonResponseService $jsonResponseService,
        private HumidityMetricsAlertService $humidityMetricsAlertService,
    ) {
    }

    /**
     * @Route("/api/humidity", name="postHumidity", methods={"POST"})
     */
    public function postHumidity(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);

        $humidityMetric = new HumidityMetric(
            (float) $data["value"],
            $user,
        );

        $this->humidityMetricRepository->add($humidityMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_humidity", name="getCurrentHumidity", methods={"GET"})
     */
    public function getCurrentHumidity(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $lastHumidityValue = $this->humidityMetricRepository->findLastHumidityMetricByUser($user);

        if ($lastHumidityValue === null) {
            return new JsonResponse("no data", 404);
        }

        $humidityViewModel = new HumidityMetricViewModel(
            $lastHumidityValue->getId(),
            $lastHumidityValue->getValue(),
            $user->getId(),
            $lastHumidityValue->getCreated_at()->format('Y-m-d H:i:s'),
            $this->humidityMetricsAlertService->createAlert($lastHumidityValue),
        );
        return $this->jsonResponseService->create($humidityViewModel, 200);
    }

    /**
     * @Route("/api/humidity_historic", name="getHumidityHistoric", methods={"GET"})
     */
    public function getHumidityHistoric(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $historicValues = $this->humidityMetricRepository->findHumidityHistoric($user);

        if ($historicValues === null) {
            return new JsonResponse("no data", 404);
        }

        $humidityViewModels = [];

        foreach ($historicValues as $historicValue) {
            $humidityViewModels[] = new HumidityMetricViewModel(
                $historicValue->getId(),
                $historicValue->getValue(),
                $user->getId(),
                $historicValue->getCreated_at()->format('Y-m-d H:i:s'),
            );
        }

        return $this->jsonResponseService->create($humidityViewModels);
    }
}


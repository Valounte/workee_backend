<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
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
        private LogsServiceInterface $logsService,
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

        if (!isset($data['value'])) {
            $this->logsService->add(400, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'InvalidInputException');
            return new JsonResponse('value is required', 400);
        }

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
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'TemperatureMetricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $temperatureViewModel = new TemperatureMetricViewModel(
            $lastTemperatureValue->getId(),
            $lastTemperatureValue->getValue(),
            $user->getId(),
            $lastTemperatureValue->getCreated_at()->format('Y-m-d H:i:s'),
            $this->temperatureMetricsAlertService->createAlert($lastTemperatureValue),
        );

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO, 'TemperatureMetricFound');
        return $this->jsonResponseService->create($temperatureViewModel);
    }

    /**
     * @Route("/api/temperature_historic", name="getTemperatureHistoric", methods={"GET"})
     */
    public function getTemperatureHistoric(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $historicValues = $this->temperatureMetricRepository->findTemperatureHistoric($user);

        if ($historicValues === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'TemperatureHistoricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $temperatureViewModels = [];

        foreach ($historicValues as $historicValue) {
            $temperatureViewModels[] = new TemperatureMetricViewModel(
                $historicValue->getId(),
                $historicValue->getValue(),
                $user->getId(),
                $historicValue->getCreated_at()->format('Y-m-d H:i:s'),
            );
        }

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($temperatureViewModels);
    }
}

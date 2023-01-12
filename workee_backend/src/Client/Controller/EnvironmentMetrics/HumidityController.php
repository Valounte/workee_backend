<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
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
        private JsonResponseService $jsonResponseService,
        private HumidityMetricsAlertService $humidityMetricsAlertService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/humidity", name="postHumidity", methods={"POST"})
     */
    public function postHumidity(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['value'])) {
            $this->logsService->add(400, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'InvalidInputException');
            return new JsonResponse('value is required', Response::HTTP_BAD_REQUEST);
        }

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
        $user = $request->attributes->get('user');

        $lastHumidityValue = $this->humidityMetricRepository->findLastHumidityMetricByUser($user);

        if ($lastHumidityValue === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'HumidityMetricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $humidityViewModel = new HumidityMetricViewModel(
            $lastHumidityValue->getId(),
            $lastHumidityValue->getValue(),
            $user->getId(),
            $lastHumidityValue->getCreated_at()->format('Y-m-d H:i:s'),
            $this->humidityMetricsAlertService->createAlert($lastHumidityValue),
        );

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($humidityViewModel, 200);
    }

    /**
     * @Route("/api/humidity_historic", name="getHumidityHistoric", methods={"GET"})
     */
    public function getHumidityHistoric(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $historicValues = $this->humidityMetricRepository->findHumidityHistoric($user);

        if ($historicValues === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'HumidityHistoriqueNotFoundException');
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

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($humidityViewModels);
    }
}

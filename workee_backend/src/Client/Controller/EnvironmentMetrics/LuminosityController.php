<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Logs\Services\LogsService;
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
use App\Core\Components\EnvironmentMetrics\Entity\LuminosityMetric;
use App\Client\ViewModel\EnvironmentMetrics\LuminosityMetricViewModel;
use App\Core\Components\EnvironmentMetrics\ValueObject\LuminosityAlert;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\EnvironmentMetrics\Services\LuminosityMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\LuminosityMetricRepositoryInterface;

final class LuminosityController extends AbstractController
{
    public function __construct(
        private LuminosityMetricRepositoryInterface $luminosityMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private JsonResponseService $jsonResponseService,
        private LuminosityMetricsAlertService $luminosityMetricsAlertService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/luminosity", name="postLuminosity", methods={"POST"})
     */
    public function postLuminosity(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['value'])) {
            $this->logsService->add(400, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'InvalidInputException');
            return new JsonResponse('value is required', Response::HTTP_BAD_REQUEST);
        }

        $luminosityMetric = new LuminosityMetric(
            (float) $data["value"],
            $user,
        );

        $this->luminosityMetricRepository->add($luminosityMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_luminosity", name="getCurrentLuminosity", methods={"GET"})
     */
    public function getCurrentLuminosity(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $lastLuminosityValue = $this->luminosityMetricRepository->findLastLuminosityMetricByUser($user);

        if ($lastLuminosityValue === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'LuminosityMetricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $luminosityViewModel = new LuminosityMetricViewModel(
            $lastLuminosityValue->getId(),
            $lastLuminosityValue->getValue(),
            $user->getId(),
            $lastLuminosityValue->getCreated_at()->format('Y-m-d H:i:s'),
            $this->luminosityMetricsAlertService->createAlert($lastLuminosityValue),
        );

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($luminosityViewModel, 200);
    }

    /**
     * @Route("/api/luminosity_historic", name="getLuminosityHistoric", methods={"GET"})
     */
    public function getLuminosityHistoric(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $historicValues = $this->luminosityMetricRepository->findLuminosityHistoric($user);

        if ($historicValues === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'LuminosityHistoricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $luminosityViewModel = [];

        foreach ($historicValues as $historicValue) {
            $luminosityViewModel[] = new LuminosityMetricViewModel(
                $historicValue->getId(),
                $historicValue->getValue(),
                $user->getId(),
                $historicValue->getCreated_at()->format('Y-m-d H:i:s'),
            );
        }

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($luminosityViewModel);
    }
}

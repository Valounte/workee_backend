<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserNotFoundException;
use App\Core\Components\EnvironmentMetrics\Entity\SoundMetric;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Client\ViewModel\EnvironmentMetrics\SoundMetricViewModel;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\EnvironmentMetrics\Services\SoundMetricsAlertService;
use App\Core\Components\EnvironmentMetrics\Repository\SoundMetricRepositoryInterface;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;

final class SoundController extends AbstractController
{
    public function __construct(
        private SoundMetricRepositoryInterface $soundMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private JsonResponseService $jsonResponseService,
        private SoundMetricsAlertService $soundMetricsAlertService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/sound", name="postSound", methods={"POST"})
     */
    public function postSound(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $data = json_decode($request->getContent(), true);

        if (!isset($data["value"])) {
            $this->logsService->add(400, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'InvalidInputException');
            return $this->jsonResponseService->errorJsonResponse('Value is required', 400);
        }

        $soundMetric = new SoundMetric(
            (float) $data["value"],
            $user,
        );

        $this->soundMetricRepository->add($soundMetric, true);

        return $this->jsonResponseService->successJsonResponse('Data stored', 200);
    }

    /**
     * @Route("/api/current_sound", name="getCurrentSound", methods={"GET"})
     */
    public function getCurrentSound(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $lastSoundValue = $this->soundMetricRepository->findLastSoundMetricByUser($user);

        if ($lastSoundValue === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'SoundMetricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $soundViewModel = new SoundMetricViewModel(
            $lastSoundValue->getId(),
            $lastSoundValue->getValue(),
            $user->getId(),
            $lastSoundValue->getCreated_at()->format('Y-m-d H:i:s'),
            $this->soundMetricsAlertService->createAlert($lastSoundValue),
        );

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO, 'SoundMetricFound');
        return $this->jsonResponseService->create($soundViewModel, 200);
    }

    /**
     * @Route("/api/sound_historic", name="getSoundHistoric", methods={"GET"})
     */
    public function getSoundHistoric(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $historicValues = $this->soundMetricRepository->findSoundHistoric($user);

        if ($historicValues === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::WARNING, 'SoundHistoricNotFoundException');
            return new JsonResponse("no data", 404);
        }

        $soundViewModels = [];

        foreach ($historicValues as $historicValue) {
            $soundViewModels[] = new SoundMetricViewModel(
                $historicValue->getId(),
                $historicValue->getValue(),
                $user->getId(),
                $historicValue->getCreated_at()->format('Y-m-d H:i:s'),
            );
        }

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($soundViewModels);
    }
}

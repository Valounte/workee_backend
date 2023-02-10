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
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\EnvironmentMetrics\Repository\EnvironmentMetricsPreferencesRepositoryInterface;
use App\Core\Components\EnvironmentMetrics\UseCase\EnvironmentMetricsPreferences\EnvironmentMetricPreferenceUpdatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final class EnvironmentMetricsPreferencesController extends AbstractController
{
    public function __construct(
        private EnvironmentMetricsPreferencesRepositoryInterface $environmentMetricsPreferencesRepository,
        private JsonResponseService $jsonResponseService,
        private LogsServiceInterface $logsService,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @Route("/api/environment-metrics-preferences", name="environment-metrics-preferences", methods={"GET"})
     */
    public function getEnvironmentMetricsPreferences(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $environmentMetricsPreferences = $this->environmentMetricsPreferencesRepository->getAllEnvironmentMetricsPreferences($user);

        return $this->jsonResponseService->create($environmentMetricsPreferences);
    }

    /**
     * @Route("/api/environment-metrics-preference", name="environment-metrics-preference", methods={"PUT"})
     */
    public function updateEnvironmentMetricsPreference(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $input = json_decode($request->getContent(), true);
        $isDesactivated = $input['isDesactivated'];
        $metricType = $input['metricType'];


        $environmentMetricsPreference = $this->environmentMetricsPreferencesRepository->getOneByUserAndMetricType($user, $metricType);

        if ($environmentMetricsPreference === null) {
            $this->logsService->add(404, LogsContextEnum::ENVIRONMENT_METRICS_SETTINGS, LogsAlertEnum::WARNING, null, $user);
            return new JsonResponse('Environment metrics preference not found', Response::HTTP_NOT_FOUND);
        }

        $environmentMetricsPreference->setIsDesactivated($isDesactivated);
        $this->environmentMetricsPreferencesRepository->add($environmentMetricsPreference);

        $preferenceUpdatedEvent = new EnvironmentMetricPreferenceUpdatedEvent(
            $user->getId(),
            $metricType,
            $isDesactivated,
        );
        $this->messageBus->dispatch($preferenceUpdatedEvent);

        $this->logsService->add(200, LogsContextEnum::ENVIRONMENT_METRICS_SETTINGS, null, null, $user);

        return $this->jsonResponseService->successJsonResponse('Environment metrics preference updated', 200);
    }
}

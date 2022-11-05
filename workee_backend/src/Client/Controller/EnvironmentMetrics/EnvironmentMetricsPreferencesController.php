<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\EnvironmentMetrics\Repository\EnvironmentMetricsPreferencesRepositoryInterface;

final class EnvironmentMetricsPreferencesController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private EnvironmentMetricsPreferencesRepositoryInterface $environmentMetricsPreferencesRepository,
        private JsonResponseService $jsonResponseService,
    ) {
    }

    /**
     * @Route("/api/environment-metrics-preferences", name="environment-metrics-preferences", methods={"GET"})
     */
    public function getEnvironmentMetricsPreferences(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $environmentMetricsPreferences = $this->environmentMetricsPreferencesRepository->getAllEnvironmentMetricsPreferences($user);

        return $this->jsonResponseService->create($environmentMetricsPreferences);
    }

    /**
     * @Route("/api/environment-metrics-preference", name="environment-metrics-preference", methods={"PUT"})
     */
    public function updateEnvironmentMetricsPreference(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);
        $isDesactivated = $input['isDesactivated'];
        $metricType = $input['metricType'];

        $environmentMetricsPreference = $this->environmentMetricsPreferencesRepository->getOneByUserAndMetricType($user, $metricType);
        $environmentMetricsPreference->setIsDesactivated($isDesactivated);
        $this->environmentMetricsPreferencesRepository->add($environmentMetricsPreference);

        return $this->jsonResponseService->successJsonResponse('Environment metrics preference updated', 200);
    }
}

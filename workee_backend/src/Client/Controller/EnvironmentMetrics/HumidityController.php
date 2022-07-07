<?php

namespace App\Client\Controller\EnvironmentMetrics;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\EnvironmentMetrics\Entity\HumidityMetric;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Client\ViewModel\EnvironmentMetrics\HumidityMetricViewModel;
use App\Core\Components\EnvironmentMetrics\Repository\HumidityMetricRepositoryInterface;

final class HumidityController extends AbstractController
{
    public function __construct(
        private HumidityMetricRepositoryInterface $humidityMetricRepository,
        private UserRepositoryInterface $userRepositoryInterface,
        private CheckUserPermissionsService $checkUserPermissionsService,
    ) {
    }

    /**
     * @Route("/api/humidity", name="postHumidity", methods={"POST"})
     */
    public function postHumidity(Request $request): JsonResponse
    {
        try {
            $jwt = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);
        $user = $this->userRepositoryInterface->findUserById($jwt["id"]);

        $humidityMetric = new HumidityMetric(
            (float) $data["value"],
            $user,
        );

        $this->humidityMetricRepository->add($humidityMetric, true);

        return new JsonResponse("data stored", 201);
    }

    /**
     * @Route("/api/current_humidity", name="getCurrentHumidity", methods={"GET"})
     */
    public function getCurrentHumidity(Request $request): JsonResponse
    {
        try {
            $jwt = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $user = $this->userRepositoryInterface->findUserById($jwt["id"]);
        $lastHumidityValue = $this->humidityMetricRepository->findLastHumidityMetricByUser($user);

        if ($lastHumidityValue === null) {
            return new JsonResponse("no data", 404);
        }

        $humidityViewModel = new HumidityMetricViewModel(
            $lastHumidityValue->getId(),
            $lastHumidityValue->getValue(),
            $user->getId(),
        );

        return new JsonResponse($humidityViewModel, 200);
    }
}

<?php

namespace App\Core\Components\EnvironmentMetrics\UseCase\EnvironmentMetricsPreferences;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\EnvironmentMetrics\UseCase\EnvironmentMetricsPreferences\EnvironmentMetricPreferenceUpdatedEvent;

final class EnvironmentMetricPreferenceUpdatedListener implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private HubInterface $hub,
        private string $mercureHubUrl,
        private TokenService $tokenService,
    ) {
    }

    public function __invoke(EnvironmentMetricPreferenceUpdatedEvent $event): void
    {
        $user = $this->userRepository->findUserById($event->getUserId());
        $jwt = $this->tokenService->createLoginToken($user);
        $update = new Update(
            $this->mercureHubUrl . '/metrics-preferences' . '/' . $jwt,
            json_encode([
                'metricType' => $event->getMetricType(),
                'isDesactivated' => $event->getIsDesactivated(),
            ])
        );
        $this->hub->publish($update);
    }
}

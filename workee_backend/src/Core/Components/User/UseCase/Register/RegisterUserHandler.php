<?php

namespace App\Core\Components\User\UseCase\Register;

use App\Core\Components\EnvironmentMetrics\Entity\EnvironmentMetricsPreferences;
use App\Core\Components\EnvironmentMetrics\Repository\EnvironmentMetricsPreferencesRepositoryInterface;
use Throwable;
use App\Core\Components\User\Entity\User;
use App\Core\Components\User\Entity\UserTeam;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Infrastructure\User\Services\CheckUserInformationService;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use App\Core\Components\Notification\Entity\NotificationPreferences;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Core\Components\EnvironmentMetrics\ValueObject\Enum\AlertLevelEnum;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Core\Components\Notification\Repository\NotificationPreferencesRepositoryInterface;

final class RegisterUserHandler implements MessageHandlerInterface
{
    public function __construct(
        private CheckUserInformationService $checkUserInformationService,
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private UserTeamRepositoryInterface $userTeamRepository,
        private TeamRepositoryInterface $teamRepository,
        private JobRepositoryInterface $jobRepository,
        private NotificationPreferencesRepositoryInterface $notificationPreferencesRepository,
        private EnvironmentMetricsPreferencesRepositoryInterface $environmentMetricsPreferencesRepository,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $this->checkUserInformationService->checkUserInformation(
            $command->getEmail(),
            $command->getPassword()
        );

        $user = new User(
            $command->getEmail(),
            $command->getFirstname(),
            $command->getLastname(),
            $command->getCompany(),
        );

        if ($command->getPassword() != null) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $command->getPassword()));
        }

        if ($command->getJobId() != null) {
            $user->setJob($this->jobRepository->findOneById($command->getJobId()));
        }

        $this->userRepository->save($user);

        $this->createDefaultNotificationPreferences($user);
        $this->createDefaultEnvironmentMetricsPreferences($user);

        if ($command->getTeamsId() != null) {
            foreach ($command->getTeamsId() as $teamId) {
                $team = $this->teamRepository->findOneById($teamId);
                $userTeam = new UserTeam($user, $team);
                $this->userTeamRepository->add($userTeam);
            }
        }
    }

    private function createDefaultNotificationPreferences(User $user): void
    {
        $defaultNotifications = [];
        $defaultNotifications[] = new NotificationPreferences($user, NotificationAlertLevelEnum::NORMAL_ALERT);
        $defaultNotifications[] = new NotificationPreferences($user, NotificationAlertLevelEnum::IMPORTANT_ALERT);
        $defaultNotifications[] = new NotificationPreferences($user, NotificationAlertLevelEnum::URGENT_ALERT);

        foreach ($defaultNotifications as $notification) {
            $this->notificationPreferencesRepository->add($notification);
        }
    }

    private function createDefaultEnvironmentMetricsPreferences(User $user): void
    {
        $defaultPreferences = [];
        $defaultPreferences[] = new EnvironmentMetricsPreferences($user, 'TEMPERATURE');
        $defaultPreferences[] = new EnvironmentMetricsPreferences($user, 'SOUND');
        $defaultPreferences[] = new EnvironmentMetricsPreferences($user, 'LUMINOSITY');
        $defaultPreferences[] = new EnvironmentMetricsPreferences($user, 'HUMIDITY');

        foreach ($defaultPreferences as $preference) {
            $this->environmentMetricsPreferencesRepository->add($preference);
        }
    }
}

<?php

namespace App\Core\Components\User\Service;

use App\Core\Components\User\Entity\User;
use App\Client\ViewModel\Job\JobViewModel;
use App\Client\ViewModel\Team\TeamViewModel;
use App\Client\ViewModel\User\UserViewModel;
use App\Client\ViewModel\Job\PermissionViewModel;
use App\Client\ViewModel\Company\CompanyViewModel;
use App\Infrastructure\User\Repository\UserRepository;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;

final class GetUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private JobRepositoryInterface $jobRepository,
        private JobPermissionRepositoryInterface $jobPermissionRepository,
        private UserTeamRepositoryInterface $userTeamRepository,
        private CompanyRepositoryInterface $companyRepository,
        private TeamRepositoryInterface $teamRepository,
    ) {
    }

    public function createUserViewModel(User $user): UserViewModel
    {
        $job = $user->getJob();
        $permissionsViewModels = null;
        $jobViewModel = null;
        $teamsViewModels = null;
        $teams = $this->userTeamRepository->findTeamsByUser($user);

        if (isset($job)) {
            $permissions = $this->jobPermissionRepository->findPermissionsByJob($user->getJob());
            $permissionsViewModels = $this->getPermissionsViewModels($permissions);

            $jobViewModel = new JobViewModel(
                $job->getId(),
                $job->getName(),
                $job->getDescription(),
                new CompanyViewModel(
                    $job->getCompany()->getId(),
                    $job->getCompany()->getCompanyName(),
                ),
                $permissionsViewModels,
            );
        }

        if (isset($teams)) {
            $teamsViewModels = $this->getTeamsViewModels($teams);
        }


        $userViewModel = new UserViewModel(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstname(),
            $user->getLastname(),
            new CompanyViewModel($user->getCompany()->getId(), $user->getCompany()->getCompanyName()),
            $user->getPicture(),
            $teamsViewModels,
            $jobViewModel,
        );

        return $userViewModel;
    }

    private function getPermissionsViewModels(array $permissions): array
    {
        $permissionViewModels = [];
        foreach ($permissions as $permission) {
            array_push(
                $permissionViewModels,
                new PermissionViewModel($permission->getId(), $permission->getName(), $permission->getContext())
            );
        }
        return $permissionViewModels;
    }

    private function getTeamsViewModels(array $teams): array
    {
        $teamValueObject = [];
        foreach ($teams as $team) {
            $companyValueObject = new CompanyViewModel($team->getCompany()->getId(), $team->getCompany()->getCompanyName());
            array_push($teamValueObject, new TeamViewModel($team->getId(), $team->getTeamName(), $team->getDescription(), $companyValueObject));
        }

        return $teamValueObject;
    }
}

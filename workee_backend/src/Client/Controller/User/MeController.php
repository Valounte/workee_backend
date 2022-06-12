<?php

namespace App\Client\Controller\User;

use Exception;
use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use App\Core\Components\Job\Entity\JobPermission;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Infrastructure\Job\Repository\JobPermissionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\Response\Services\JsonResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MeController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserPasswordHasherInterface $passwordHasher,
        private CompanyRepositoryInterface $companyRepository,
        private TeamRepositoryInterface $teamRepository,
        private UserTeamRepositoryInterface $userTeamRepository,
        private TokenService $tokenService,
        private JobRepositoryInterface $jobRepository,
        private JobPermissionRepositoryInterface $jobPermissionRepository,
    ) {
    }

    /**
     * @Route("api/me", name="get_me"),
     * methods("GET")
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unauthorized', 401);
        }
        $user = $this->userRepository->findUserById($jwt['id']);
        $teams = $this->userTeamRepository->findTeamsByUser($user);
        $permissions = $this->jobPermissionRepository->findPermissionsByJob($user->getJob());
        $userViewModel = UserViewModel::createByUser($user, $this->parseTeams($teams), $this->parsePermissions($permissions));

        return new JsonResponse($userViewModel);
    }

    private function parsePermissions(array $permissions): array
    {
        $parsedPermissions = [];
        foreach ($permissions as $permission) {
            array_push($parsedPermissions, $permission->getName()->name);
        }
        return $parsedPermissions;
    }

    private function parseTeams(array $teams): ?array
    {
        if (empty($teams)) {
            return null;
        }

        $parsedTeams = [];
        foreach ($teams as $team) {
            array_push($parsedTeams, ['id' => $team->getId(), 'name' => $team->getTeamName()]);
        }
        return $parsedTeams;
    }
}

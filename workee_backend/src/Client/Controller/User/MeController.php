<?php

namespace App\Client\Controller\User;

use Exception;
use App\Client\ViewModel\UserViewModel;
use App\Core\Services\JsonResponseService;
use App\Infrastructure\Services\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Repository\TeamRepository;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Repository\CompanyRepository;
use App\Infrastructure\Repository\UserTeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MeController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserPasswordHasherInterface $passwordHasher,
        private CompanyRepository $companyRepository,
        private TeamRepository $teamRepository,
        private UserTeamRepository $userTeamRepository,
        private TokenService $tokenService,
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
            return $this->jsonResponseService->errorJsonResponse('Unauthorized');
        }

        $userViewModel = new UserViewModel($this->userRepository->findUserById($jwt['id']), $this->userTeamRepository);

        return $this->jsonResponseService->userViewModelJsonResponse($userViewModel);
    }
}

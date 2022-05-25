<?php

namespace App\Client\Controller\User;

use Exception;
use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
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

        $userViewModel = UserViewModel::createByUser($this->userRepository->findUserById($jwt['id']), $this->userTeamRepository);

        return $this->jsonResponseService->userViewModelJsonResponse($userViewModel);
    }
}

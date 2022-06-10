<?php

namespace App\Client\Controller\User;

use Exception;
use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\User\Entity\UserTeam;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Services\CheckUserInformationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserPasswordHasherInterface $passwordHasher,
        private CompanyRepositoryInterface $companyRepository,
        private TeamRepositoryInterface $teamRepository,
        private UserTeamRepositoryInterface $userTeamRepository,
        private TokenService $tokenService,
        private MailerInterface $mailer,
        private CheckUserInformationService $checkUserInformationService,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @Route("api/user/{id}", name="get_user_by_id"),
     * methods("GET")
     */
    public function getUserById(int $id, Request $request): JsonResponse
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unautorized', 401);
        }

        return $this->jsonResponseService->userViewModelJsonResponse(
            UserViewModel::createByUser($this->userRepository->findUserById($id), $this->userTeamRepository)
        );
    }

    /**
     * @Route("api/user", name="create_user"),
     * methods("POST")
     */
    public function createUser(Request $request): Response
    {
        $userData = json_decode($request->getContent(), true);

        $registerUserCommand = new RegisterUserCommand(
            $userData["firstname"],
            $userData["lastname"],
            $userData["email"],
            $this->companyRepository->findOneById($userData["company"]),
            $userData["password"],
        );

        try {
            $this->messageBus->dispatch($registerUserCommand);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse($e->getMessage(), 400);
        }

        return $this->jsonResponseService->successJsonResponse("User successfully created !", 201);
    }

    /**
     * @Route("api/user/team", name="add_to_team"),
     * methods("POST")
     */
    public function addToTeam(Request $request): Response
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unauthorized', 401);
        }

        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->findUserById($data["userId"]);
        $team = $this->teamRepository->findOneById($data["teamId"]);

        $userTeam = new UserTeam(
            $user,
            $team,
        );

        $this->userTeamRepository->add($userTeam);

        return $this->jsonResponseService->successJsonResponse("user successfully added to the team !", 200);
    }

    /**
     * @Route("api/users/company", name="get_user_by_company"),
     * methods("GET")
     */
    public function getUserByCompany(Request $request): JsonResponse
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unauthorized', 401);
        }

        $users = $this->userRepository->findByCompany($jwt['company']);

        $company = $this->companyRepository->findOneById($jwt['company']);

        $usersViewModels = [];

        foreach ($users as $user) {
            $usersViewModels[] = new UserViewModel(
                $user['id'],
                $user['email'],
                $user['firstname'],
                $user['lastname'],
                $company->getId(),
                $this->userTeamRepository,
            );
        }

        return new JsonResponse($usersViewModels, 200);
    }
}

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
use App\Core\Components\User\Service\GetUserService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserInformationException;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserInformationService;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
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
        private GetUserService $getUserService,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("api/user/picture", name="set_picture"),
     * methods("POST")
     */
    public function setPicture(Request $request): JsonResponse
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
        $data = json_decode($request->getContent(), true);

        $user->setPicture($data['picture']);

        $this->userRepository->save($user);

        return $this->jsonResponseService->successJsonResponse('Picture updated', 200);
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

        $wantedUser = $this->userRepository->findUserById($id);

        if ($wantedUser === null) {
            return $this->jsonResponseService->errorJsonResponse('User not found', 404);
        }

        $user = $this->getUserService->createUserViewModel($wantedUser);

        return $this->jsonResponseService->create($user);
    }


    /**
     * @Route("api/user", name="create_user"),
     * methods("POST")
     */
    public function createUser(Request $request): JsonResponse
    {
        $userData = json_decode($request->getContent(), true);

        $registerUserCommand = new RegisterUserCommand(
            $userData["firstname"],
            $userData["lastname"],
            $userData["email"],
            $this->companyRepository->findOneById($userData["company"]),
            password: $userData["password"],
        );

        try {
            $this->messageBus->dispatch($registerUserCommand);
        } catch (UserInformationException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        return $this->jsonResponseService->successJsonResponse("User successfully created !", 201);
    }

    /**
     * @Route("api/add-to-team", name="add_to_team"),
     * methods("POST")
     */
    public function addToTeam(Request $request): Response
    {
        try {
            $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
     * @Route("api/users", name="get_users"),
     * methods("GET")
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $me = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $users = $this->userRepository->findByCompany($me->getCompany());
        $usersViewModels = [];

        foreach ($users as $user) {
            $usersViewModels[] = $this->getUserService->createUserViewModel($user);
        }

        $this->logsService->add(200, LogsContextEnum::USER, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($usersViewModels);
    }
}

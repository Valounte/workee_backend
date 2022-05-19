<?php

namespace App\Client\Controller\User;

use Exception;
use App\Core\Entity\User;
use App\Core\Entity\UserTeam;
use App\Client\ViewModel\UserViewModel;
use App\Core\Services\JsonResponseService;
use App\Infrastructure\Services\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Repository\TeamRepository;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Repository\CompanyRepository;
use App\Infrastructure\Repository\UserTeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
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
        $returnValue = $this->createResponseIfDataAreNotValid($userData);

        if ($returnValue instanceof Response) {
            return $returnValue;
        }

        $user = new User(
            $userData["email"],
            $userData["firstname"],
            $userData["lastname"],
            $this->companyRepository->findOneById($userData["company"]),
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, $userData["password"]));

        $this->userRepository->save($user);

        return new Response('User created');
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

        return $this->jsonResponseService->successJsonResponse("user added to team");
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

        return new JsonResponse($usersViewModels);
    }

    private function createResponseIfDataAreNotValid(array $userData): bool|Response
    {
        if ($this->userRepository->findUserByEmail($userData["email"]) != null) {
            return new Response('Email already used', 409);
        }

        if (!filter_var($userData["email"], FILTER_VALIDATE_EMAIL)) {
            return new Response('Bad email', 400);
        }

        if (!$this->checkPasswordFormat($userData["password"])) {
            return new Response('Password format not valid', 400);
        }

        if ($this->companyRepository->findOneById($userData["company"] == null)) {
            return new Response('Company does not exist', 400);
        }

        return true;
    }

    private function checkPasswordFormat(string $password): bool
    {
        $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';

        if (!preg_match($pattern, $password)) {
            return false;
        }

        return true;
    }
}

<?php

namespace App\Controller;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\ViewModel\UserViewModel;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Services\JsonResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private UserRepository $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserPasswordHasherInterface $passwordHasher,
        private TeamRepository $teamRepository,
        private CompanyRepository $companyRepository,
    ) {
    }


    /**
     * @Route("api/user/{id}", name="get_user_by_id"),
     * methods("GET")
     */
    public function getUserById(int $id): JsonResponse
    {
        return $this->jsonResponseService->userViewModelJsonResponse(
            new UserViewModel($this->userRepository->findUserById($id))
        );
    }


    /**
     * @Route("api/user", name="create_user"),
     * methods("POST")
     */
    public function createUserTest6(Request $request): Response
    {

        $userData = json_decode($request->getContent(), true);
        $returnValue = $this->createResponseIfDataAreNotValid($userData);

        if ($returnValue instanceof Response) {
            return $returnValue;
        }

        $team = $returnValue instanceof Team ? $returnValue : null;

        $user = new User(
            $userData["email"],
            $userData["firstname"],
            $userData["lastname"],
            $this->companyRepository->findOneById($userData["company"]),
            $team,
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, $userData["password"]));

        $this->userRepository->save($user);
        return new Response('User created');
    }

    /**
     * @Route("api/users/{teamId}", name="get_users_by_team"),
     * methods("GET")
     */
    public function getUsersByTeam(int $teamId): JsonResponse
    {
        $users = $this->userRepository->findByTeamId($teamId);

        $usersViewModel = array_map(
            static fn ($i): UserViewModel =>
            new UserViewModel(new User($i["email"], $i["firstname"], $i["lastname"], $i["company"])),
            $users,
        );

        return $this->jsonResponseService->usersViewModelJsonResponse($usersViewModel, $teamId);
    }


    private function createResponseIfDataAreNotValid(array $userData): bool|Response|Team
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

        if (isset($userData["team"])) {
            $team = $this->teamRepository->findOneById($userData["team"]);

            if ($team == null)
                return new Response("Team does not exist", 400);
            else
                return $team;
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

<?php
namespace App\Client\Controller;

use App\Client\ViewModel\UserViewModel;
use App\Core\Entity\User;
use App\Core\Services\JsonResponseService;
use App\Infrastructure\Repository\CompanyRepository;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserPasswordHasherInterface $passwordHasher,
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

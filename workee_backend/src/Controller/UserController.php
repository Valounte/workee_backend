<?php

namespace App\Controller;

use App\Entity\User;
use App\ViewModel\UserViewModel;
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
    ) {
    }

    /**
     * @Route("api/user/{id}", name="getUserById"),
     * methods("GET")
     */
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userRepository->findUserById($id);
        
        return $this->jsonResponseService->userViewModelJsonResponse(new UserViewModel($user));
    }

    /**
     * @Route("api/user", name="create_user"),
     * methods("POST")
     */
    public function createUser(Request $request): Response
    {

        $userData = json_decode($request->getContent(), true);
        $errorResponse = $this->createResponseIfDataAreNotValid($userData);

        if ($errorResponse instanceof Response) {
            return $errorResponse;
        }

        $user = new User(
            $userData["email"],
            $userData["firstname"],
            $userData["lastname"],
            $userData["team"],
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

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
    ){
    }

    /**
     * @Route("/user/{id}", name="getUserById"),
     * methods("GET")
     */
    public function getUserById(int $id): JsonResponse
    { 
        $user = $this->userRepository->findUserById($id);
        $userViewModel = new UserViewModel($user);
        
        return $this->jsonResponseService->userViewModelJsonResponse($userViewModel);
    }

    /**
     * @Route("/user", name="createUser"),
     * methods("POST")
     */
    public function createUser(Request $request): Response
    { 
        $userData = json_decode($request->getContent(), true);
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
}
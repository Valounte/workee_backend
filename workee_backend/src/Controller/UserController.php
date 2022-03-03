<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\ViewModel\UserViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id}", name="getUserById"),
     * methods("GET")
     */
    public function getUserById(int $id, UserRepository $userRepository): JsonResponse
    { 
        $user = $userRepository->findUserById($id);
        $userViewModel = new UserViewModel($user);
        
        return $userViewModel->createJsonResponse();
    }

    /**
     * @Route("/user", name="createUser"),
     * methods("POST")
     */
    public function createUser(Request $request, UserRepository $userRepository): Response
    { 
        $userData = json_decode($request->getContent(), true);
        $user = new User(
            $userData["email"],
            $userData["firstname"], 
            $userData["lastname"], 
            $userData["team"]
        );
        $userRepository->save($user);       
        return new Response('User created');
    }
}
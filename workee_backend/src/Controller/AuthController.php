<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Repository\UserRepository;
use App\Services\JsonResponseService;
use App\ViewModel\UserViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $encoder,
        private JsonResponseService $jsonResponseService,
    ){   
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        $userData = json_decode($request->getContent(), true);
        $user = $this->userRepository->findUserByEmail($userData['email']);
        
        if (!$user || !$this->encoder->isPasswordValid($user, $userData['password'])) {
            return $this->json(
                ['message' => 'email or password is wrong.'],
                400,
            );
        }

        $jwt = JWT::encode(
            $this->jsonResponseService->userViewModelJsonResponse(new UserViewModel($user)), 
            $this->getParameter('jwt_secret'), 'HS256'
        );

        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }
}

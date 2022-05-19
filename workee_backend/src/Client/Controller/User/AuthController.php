<?php

namespace App\Client\Controller\User;

use App\Core\Services\EmailService;
use Firebase\JWT\JWT;
use Symfony\Component\Mime\Email;
use App\Core\Services\JsonResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $encoder,
        private JsonResponseService $jsonResponseService,
        private EmailService $emailService,
    ) {
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
            ["id" => $user->getId(), "company" => $user->getCompany()->getId()],
            'jwt_secret',
            'HS256'
        );

        return $this->json([
            'message' => 'success!',
            'token' => sprintf('Bearer %s', $jwt),
        ]);
    }

    /**
     * @Route("/api/email", name="email", methods={"POST"})
     */
    public function sendEmail(Request $request): Response
    {
        $this->emailService->sendRegistrationEmail();

        return $this->json([
            'message' => 'success!',
        ]);
    }
}

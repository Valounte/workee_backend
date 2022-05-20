<?php

namespace App\Client\Controller\User;

use Exception;
use Firebase\JWT\JWT;
use App\Core\Entity\User;
use App\Core\Services\CheckUserInformationService;
use App\Core\Services\JsonResponseService;
use App\Infrastructure\Services\TokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Services\RegistrationEmailGenerator;
use App\Infrastructure\Repository\CompanyRepository;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $encoder,
        private JsonResponseService $jsonResponseService,
        private TokenService $tokenService,
        private UserPasswordHasherInterface $passwordHasher,
        private CheckUserInformationService $checkUserInformationService,
        private MailerInterface $mailer,
        private CompanyRepository $companyRepository,
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
     * @Route("/api/registration/password", name="registrationEditPassword", methods={"POST"})
     */
    public function registrationEditPassword(Request $request): Response
    {
        $userData = json_decode($request->getContent(), true);

        try {
            $token = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Invalid token', 400);
        }

        $user = $this->userRepository->findUserByEmail($token["email"]);

        $user->setPassword($this->passwordHasher->hashPassword($user, $userData["password"]));

        $this->userRepository->save($user);

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
     * @Route("api/invite/user", name="invite_user"),
     * methods("POST")
     */
    public function inviteUser(Request $request): Response
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unauthorized', 400);
        }

        $userData = json_decode($request->getContent(), true);
        $returnValue = $this->checkUserInformationService->createResponseIfDataAreNotValid($userData);

        if ($returnValue instanceof Response) {
            return $returnValue;
        }

        $user = new User(
            $userData["email"],
            $userData["firstname"],
            $userData["lastname"],
            $this->companyRepository->findOneById($jwt["company"]),
        );

        $this->userRepository->save($user);

        $token = JWT::encode(
            ["email" => $userData["email"]],
            'jwt_secret',
            'HS256'
        );

        $email = RegistrationEmailGenerator::generate($user, $token);

        $this->mailer->send($email);

        return $this->jsonResponseService->successJsonResponse("User successfully invited !", 201);
    }
}

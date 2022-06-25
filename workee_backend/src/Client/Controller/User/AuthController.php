<?php

namespace App\Client\Controller\User;

use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserInformationException;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserInformationService;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Core\Components\User\UseCase\Register\SendInviteEmailCommand;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

class AuthController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $encoder,
        private JsonResponseService $jsonResponseService,
        private TokenService $tokenService,
        private UserPasswordHasherInterface $passwordHasher,
        private CheckUserInformationService $checkUserInformationService,
        private MailerInterface $mailer,
        private CompanyRepositoryInterface $companyRepository,
        private MessageBusInterface $messageBus,
        private CheckUserPermissionsService $checkUserPermissionsService,
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
     * @Route("/api/registration/password", name="registrationEditPassword", methods={"PUT"})
     */
    public function registrationEditPassword(Request $request): Response
    {
        $userData = json_decode($request->getContent(), true);

        try {
            $token = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse($e->getMessage(), 400);
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
    public function inviteUser(Request $request): JsonResponse
    {
        try {
            $jwt = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request, PermissionNameEnum::CREATE_USER);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $userData = json_decode($request->getContent(), true);
        $company = $this->companyRepository->findOneById($jwt["company"]);

        $registerUserCommand = new RegisterUserCommand(
            $userData["firstname"],
            $userData["lastname"],
            $userData["email"],
            $company,
        );

        try {
            $this->messageBus->dispatch($registerUserCommand);
        } catch (Throwable $e) {
            return new JsonResponse($e->getPrevious()->getMessage(), $e->getPrevious()->getCode());
        }

        try {
            $this->messageBus->dispatch(new SendInviteEmailCommand($userData["email"]));
        } catch(TransportExceptionInterface $e) {
            return new JsonResponse("Email sending failed", 500);
        }

        return $this->jsonResponseService->successJsonResponse("User successfully invited !", 201);
    }
}

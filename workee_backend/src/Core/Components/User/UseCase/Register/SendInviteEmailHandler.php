<?php

namespace App\Core\Components\User\UseCase\Register;

use Firebase\JWT\JWT;
use Symfony\Component\Mailer\MailerInterface;
use App\Infrastructure\Repository\UserRepository;
use App\Infrastructure\Token\Services\TokenService;
use App\Client\Emails\User\RegistrationEmailGenerator;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Core\Components\User\UseCase\Register\SendInviteEmailCommand;

final class SendInviteEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private TokenService $tokenService,
        private MailerInterface $mailer,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(SendInviteEmailCommand $command): void
    {
        $token = JWT::encode(
            ["email" => $command->getEmail()],
            'jwt_secret',
            'HS256'
        );

        $user = $this->userRepository->findUserByEmail($command->getEmail());

        $email = RegistrationEmailGenerator::generate($user, $token);

        $this->mailer->send($email);
    }
}

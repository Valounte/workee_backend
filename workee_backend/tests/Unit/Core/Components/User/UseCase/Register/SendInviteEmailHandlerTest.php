<?php

namespace App\Tests\Unit\Core\Components\User\UseCase\Register;

use App\Tests\Unit\AbstractTestCase;
use App\Core\Components\User\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Infrastructure\Token\Services\TokenService;
use App\Client\Emails\User\RegistrationEmailGenerator;
use App\Infrastructure\User\Repository\UserRepository;
use App\Core\Components\User\UseCase\Register\SendInviteEmailCommand;
use App\Core\Components\User\UseCase\Register\SendInviteEmailHandler;
use App\Tests\Unit\StubUserFactory;

final class SendInviteEmailHandlerTest extends AbstractTestCase
{
    private TokenService $tokenService;

    private MailerInterface $mailer;

    private UserRepository $userRepository;

    private SendInviteEmailHandler $handler;

    protected function setUp(): void
    {
        $this->tokenService = new TokenService();
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->handler = new SendInviteEmailHandler(
            $this->tokenService,
            $this->mailer,
            $this->userRepository,
        );

        parent::setUp();
    }

    public function test_send_invitation_email(): void
    {
        $command = new SendInviteEmailCommand('email@gmail.com');
        $user = StubUserFactory::create(id: 1, email: 'email@gmail.com');

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($command->getEmail())
            ->willReturn($user);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->expectedEmail($user));

        $this->handler->__invoke($command);
    }

    private function expectedEmail(User $user): TemplatedEmail
    {
        return RegistrationEmailGenerator::generate($user, $this->tokenService->create(["email" => $user->getEmail()]));
    }
}

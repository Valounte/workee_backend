<?php

namespace App\Core\Components\User\UseCase\Register;

use App\Core\Components\User\Entity\User;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Infrastructure\User\Services\CheckUserInformationService;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

final class RegisterUserHandler implements MessageHandlerInterface
{
    public function __construct(
        private CheckUserInformationService $checkUserInformationService,
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {
        $this->checkUserInformationService->checkUserInformation(
            $command->getEmail(),
            $command->getPassword()
        );

        $user = new User(
            $command->getEmail(),
            $command->getFirstname(),
            $command->getLastname(),
            $command->getCompany(),
        );

        if ($command->getPassword() != null) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $command->getPassword()));
        }

        $this->userRepository->save($user);
    }
}

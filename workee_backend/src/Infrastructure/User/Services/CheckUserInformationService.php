<?php

namespace App\Infrastructure\User\Services;

use App\Infrastructure\User\Exceptions\UserInformationException;
use App\Infrastructure\User\Repository\UserRepository;

final class CheckUserInformationService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function checkUserInformation(string $email, ?string $password = null): void
    {
        if ($this->userRepository->findUserByEmail($email) != null) {
            throw UserInformationException::emailAlreadyUsedException();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw UserInformationException::invalidEmailException();
        }

        if (isset($password)) {
            if (!$this->checkPasswordFormat($password)) {
                throw UserInformationException::invalidPasswordException();
            }
        }
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

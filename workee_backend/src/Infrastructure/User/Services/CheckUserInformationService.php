<?php

namespace App\Infrastructure\User\Services;

use Symfony\Component\HttpFoundation\Response;
use App\Infrastructure\User\Repository\UserRepository;

final class CheckUserInformationService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function checkUserInformation(string $email, ?string $password = null): string
    {
        if ($this->userRepository->findUserByEmail($email) != null) {
            return 'Email already used';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email not valid';
        }

        if (isset($password)) {
            if (!$this->checkPasswordFormat($password)) {
                return 'Password format not valid';
            }
        }
        return '';
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

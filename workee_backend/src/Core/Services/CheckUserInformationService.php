<?php

namespace App\Core\Services;

use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

final class CheckUserInformationService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function createResponseIfDataAreNotValid(array $userData): bool|Response
    {
        if ($this->userRepository->findUserByEmail($userData["email"]) != null) {
            return new Response('Email already used', 400);
        }

        if (!filter_var($userData["email"], FILTER_VALIDATE_EMAIL)) {
            return new Response('Email not valid', 400);
        }

        if (isset($userData["password"])) {
            if (!$this->checkPasswordFormat($userData["password"])) {
                return new Response('Password format not valid', 400);
            }
        }
        return true;
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

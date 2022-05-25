<?php

namespace App\Infrastructure\Response\Services;

use App\Client\ViewModel\User\UserViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseService
{
    public function userViewModelJsonResponse(UserViewModel $user): JsonResponse
    {
        $user = [
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "teams" => $user->getTeams(),
            "company" => $user->getCompanyId(),
        ];

        return new JsonResponse(['user' => $user], 200);
    }

    public function successJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }

    public function errorJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }
}
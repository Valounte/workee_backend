<?php

namespace App\Core\Services;

use App\Client\ViewModel\UserViewModel;
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
            "company" => $user->getCompany(),
        ];

        return new JsonResponse(['status' => "201", 'user' => $user], 201);
    }

    public function successJsonResponse(string $message): JsonResponse
    {
        return new JsonResponse(['status' => "201", 'message' => $message], 201);
    }

    public function errorJsonResponse(string $message): JsonResponse
    {
        return new JsonResponse(['status' => "400", 'message' => $message], 400);
    }
}

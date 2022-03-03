<?php

namespace App\Services;

use App\ViewModel\UserViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseService
{
    public function userViewModelJsonResponse(UserViewModel $user): JsonResponse
    {
        $user = array(
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "team" => $user->getTeam(),
        );

        return new JsonResponse(array('status' => "201", 'user' => $user), 201);
    }
}
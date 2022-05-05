<?php
namespace App\Core\Services;



use App\Client\ViewModel\UserViewModel;
use App\Infrastructure\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseService
{
    public function userViewModelJsonResponse(UserViewModel $user): JsonResponse
    {
        $user = [
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "company" => $user->getCompany(),
        ];

        return new JsonResponse(['status' => "201", 'user' => $user], 201);
    }
}

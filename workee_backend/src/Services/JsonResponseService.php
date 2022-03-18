<?php

namespace App\Services;

use App\ViewModel\UserViewModel;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseService
{
    public function __construct(
        private TeamRepository $teamRepository,
    ){   
    }

    public function userViewModelJsonResponse(UserViewModel $user): JsonResponse
    {
        $user = array(
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "team" => $user->getTeam(),
            "company" => $user->getCompany(),
        );

        return new JsonResponse(array('status' => "201", 'user' => $user), 201);
    }

    public function usersViewModelJsonResponse(array $users, ?int $teamId = null): JsonResponse
    {
        $team = isset($teamId) ? $this->teamRepository->findOneById($teamId) : null; 

        foreach($users as $key => $value) {
            $mappedUsers[$key] = array(
                "email" => $value->getEmail(),
                "firstname" => $value->getFirstname(),
                "lastname" => $value->getLastname(),
                "team" => $team?->getName(),
            );
        }

        return new JsonResponse($mappedUsers, 201);
    }
}
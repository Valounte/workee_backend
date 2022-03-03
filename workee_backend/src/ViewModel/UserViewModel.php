<?php

namespace App\ViewModel;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserViewModel
{
    public function __construct(
        private User $user,
    ) {
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    public function getFirstname(): string
    {
        return $this->user->getFirstname();
    }

    public function getLastname(): string
    {
        return $this->user->getLastname();
    }

    public function getTeam(): string
    {
        return $this->user->getTeam();
    }

    public function createJsonResponse(): JsonResponse
    {
        $user = array(
            "email" => $this->getEmail(),
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname(),
            "team" => $this->getTeam(),
        );

        return new JsonResponse(array('status' => "201", 'user' => $user), 201);
    }
}
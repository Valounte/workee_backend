<?php

namespace App\ViewModel;

use App\Entity\User;

final class UserViewModel
{
    private string $email;

    private string $firstname;

    private string $lastname;

    private ?string $team = null;

    public function __construct(private User $user)
    {
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
        $this->team = $user->getTeam();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getTeam(): ?int
    {
        return $this->team;
    }
}
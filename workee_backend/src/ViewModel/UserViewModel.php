<?php

namespace App\ViewModel;

use App\Entity\Company;
use App\Entity\Team;
use App\Entity\User;

final class UserViewModel
{
    private string $email;

    private string $firstname;

    private string $lastname;

    private ?Team $team = null;

    private Company $company;

    public function __construct(private User $user)
    {
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
        $this->team = $user->getTeam();
        $this->company = $user->getCompany();
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

    public function getTeam(): ?Team
    {
        return $this->team;
    }


    public function getCompany(): string
    {
        return $this->company->getCompanyName();
    }
}
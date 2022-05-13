<?php

namespace App\Client\ViewModel;

use App\Core\Entity\User;
use App\Core\Entity\Company;
use App\Infrastructure\Repository\UserTeamRepository;

final class UserViewModel
{
    private string $email;

    private string $firstname;

    private string $lastname;

    private Company $company;

    private int $id;

    private array $teams;

    public function __construct(private User $user, private UserTeamRepository $userTeamRepository)
    {
        $this->id = $user->getId();
        $this->teams = $userTeamRepository->findTeamsByUser($user);
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
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

    public function getCompany(): string
    {
        return $this->company->getCompanyName();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of teams
     */ 
    public function getTeams()
    {
        return $this->teams;
    }
}

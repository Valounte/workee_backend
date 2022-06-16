<?php

namespace App\Client\ViewModel\User;

use App\Client\ViewModel\Company\CompanyViewModel;
use App\Client\ViewModel\Job\JobViewModel;
use App\Core\Components\Company\ValueObject\CompanyValueObject;
use App\Core\Components\Job\Entity\Job;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\User\Repository\UserTeamRepository;

final class UserViewModel
{
    public function __construct(
        public int $id,
        public string $email,
        public string $firstname,
        public string $lastname,
        public CompanyViewModel $company,
        public ?array $teams = null,
        public ?JobViewModel $job = null,
    ) {
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

    public function getCompany(): CompanyViewModel
    {
        return $this->company;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of teams
     */
    public function getTeams(): ?array
    {
        return $this->teams;
    }

    /**
     * Get the value of job
     */
    public function getJob()
    {
        return $this->job;
    }
}

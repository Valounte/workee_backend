<?php

namespace App\Client\ViewModel\User;

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
        public array $company,
        public ?array $job = null,
        public ?array $teams = null,
        public ?array $permissions = null,
    ) {
    }

    public static function createByUser(User $user, ?array $teams = null, ?array $permissions = null): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstname(),
            $user->getLastname(),
            ['name' => $user->getCompany()->getCompanyName(), 'id' => $user->getCompany()->getId()],
            ['name' => $user->getJob()->getName() ?? null, 'id' => $user->getJob()->getId() ?? null],
            $teams ?? null,
            $permissions ?? null,
        );
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

    public function getCompany(): array
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
}

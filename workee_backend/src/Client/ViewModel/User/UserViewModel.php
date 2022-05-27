<?php

namespace App\Client\ViewModel\User;

use App\Core\Components\User\Entity\User;
use App\Infrastructure\User\Repository\UserTeamRepository;

final class UserViewModel
{
    public string $email;

    public string $firstname;

    public string $lastname;

    public int $companyId;

    public int $id;

    public ?array $teams;

    public function __construct(
        int $id,
        string $email,
        string $firstname,
        string $lastname,
        int $companyId,
        UserTeamRepository $userTeamRepository,
    ) {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->companyId = $companyId;
        $this->id = $id;
        $this->teams = $userTeamRepository->findOneTeamByUser($id);
    }

    public static function createByUser(User $user, UserTeamRepository $userTeamRepository): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstname(),
            $user->getLastname(),
            $user->getCompany()->getId(),
            $userTeamRepository,
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

    public function getCompanyId(): int
    {
        return $this->companyId;
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

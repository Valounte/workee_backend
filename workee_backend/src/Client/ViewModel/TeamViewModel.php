<?php

namespace App\Client\ViewModel;

use App\Core\Entity\Team;
use App\Core\Entity\Company;

final class TeamViewModel {
    public function __construct(
        public int $id, 
        public string $teamName, 
        public Company $company,
    ) {
    }

    public function createByTeam(Team $team): self
    {
        return new self(
            $team->getId(),
            $team->getTeamName(),
            $team->getCompanyName(),
        );
    }

    /* get value of team's ID */
    public function getId(): int 
    {
        return $this->id;
    }

    /* get value of team's name */
    public function getTeamName(): string 
    {
        return $this->teamName;
    }

    /* get value of team's company name */
    public function getCompanyName(): string 
    {
        return $this->company->getCompanyName();
    }
}	
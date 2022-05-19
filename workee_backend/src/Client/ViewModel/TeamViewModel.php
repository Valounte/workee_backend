<?php

namespace App\Client\ViewModel;

use App\Core\Entity\Team;
use App\Core\Entity\Company;

final class TeamViewModel
{
    public function __construct(
        public int $id,
        public string $teamName,
        public int $companyId,
    ) {
    }

    public static function createByTeam(Team $team): self
    {
        $company = $team->getCompany();
        return new self(
            $team->getId(),
            $team->getTeamName(),
            $company->getId(),
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
}

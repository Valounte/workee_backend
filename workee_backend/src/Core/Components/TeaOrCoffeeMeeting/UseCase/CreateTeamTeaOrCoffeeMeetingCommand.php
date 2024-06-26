<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use DateTime;
use App\Core\Components\Team\Entity\Team;
use App\Core\Components\User\Entity\User;

final class CreateTeamTeaOrCoffeeMeetingCommand
{
    public function __construct(
        private User $initiator,
        private Team $team,
        private DateTime $date,
        private string $name,
    ) {
    }

    public function getInitiator(): User
    {
        return $this->initiator;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }
}

<?php

namespace App\Core\Components\Feedback\UseCase;

use App\Core\Components\Team\Entity\Team;

final class TeamNeedsToSendFeedbackEvent
{
    public function __construct(
        private Team $team,
        private string $sendingTime
    ) {
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getSendingTime(): string
    {
        return $this->sendingTime;
    }
}

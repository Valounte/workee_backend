<?php

namespace App\Core\Components\Feedback\UseCase;

use App\Core\Components\Team\Entity\Team;
use DateTime;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class SelectDailyFeedbackTeamPreferencesCommand
{
    public function __construct(
        private DateTime $sendingTime,
        private Team $team,
    ) {
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getSendingTime(): DateTime
    {
        return $this->sendingTime;
    }
}

<?php

namespace App\Core\Components\Feedback\UseCase;

final class TeamNeedsToSendFeedbackEvent {
    public function __construct(private int $teamId, private string $sendingTime) {}

    public function getTeamId(): int
    {
        return $this->teamId;
    }
    public function getsendingTime(): string 
    {
        return $this->sendingTime;
    }
}

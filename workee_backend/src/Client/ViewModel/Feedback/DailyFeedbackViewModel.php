<?php

namespace App\Client\ViewModel\Feedback;

use App\Client\ViewModel\Team\TeamViewModel;
use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\User\Entity\User;

final class DailyFeedbackViewModel
{
    public function __construct(
        private int $id,
        private int $satisfactionDegree,
        private TeamViewModel $team,
        private ?UserViewModel $user = null,
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of satisfactionDegree
     */
    public function getSatisfactionDegree(): int
    {
        return $this->satisfactionDegree;
    }

    /**
     * Get the value of team
     */
    public function getTeam(): TeamViewModel
    {
        return $this->team;
    }

    /**
     * Get the value of user
     */
    public function getUser(): ?UserViewModel
    {
        return $this->user;
    }
}

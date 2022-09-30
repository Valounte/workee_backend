<?php

namespace App\Client\ViewModel\Feedback;

use App\Client\ViewModel\Team\TeamViewModel;

final class LastWeekDailyFeedbackViewModel
{
    public function __construct(
        private float $averageSatisfactionDegree,
        private array $dailyFeedback,
        private TeamViewModel $team,
    ) {
    }

    /**
     * Get the value of averageSatisfactionDegree
     */
    public function getAverageSatisfactionDegree()
    {
        return round($this->averageSatisfactionDegree, 1);
    }

    /**
     * Get the value of team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Get the value of dailyFeedback
     */
    public function getDailyFeedback()
    {
        return $this->dailyFeedback;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }
}

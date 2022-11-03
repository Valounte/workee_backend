<?php

namespace App\Client\ViewModel\Feedback;

final class DailyFeedbackPreferencesViewModel
{
    public function __construct(
        private bool $isDailyFeedbackEnabled,
        private ?string $time = null,
        private ?string $cronjobTime = null,
        private ?int $teamId = null,
    ) {
    }

    /**
     * Get the value of isDailyFeedbackEnabled
     */
    public function getIsDailyFeedbackEnabled()
    {
        return $this->isDailyFeedbackEnabled;
    }

    /**
     * Get the value of time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get the value of cronjobTime
     */
    public function getCronjobTime()
    {
        return $this->cronjobTime;
    }

    /**
     * Get the value of teamId
     */
    public function getTeamId()
    {
        return $this->teamId;
    }
}

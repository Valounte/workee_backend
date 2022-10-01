<?php

namespace App\Core\Components\Feedback\Repository;

use App\Core\Components\Team\Entity\Team;
use App\Core\Components\Feedback\Entity\DailyFeedbackTeamPreferences;

interface DailyFeedbackTeamPreferencesRepositoryInterface
{
    public function add(DailyFeedbackTeamPreferences $entity, bool $flush = true): void;
    public function remove(DailyFeedbackTeamPreferences $entity, bool $flush = true): void;
    public function findOneById($id): ?DailyFeedbackTeamPreferences;
    public function findByTeam(Team $team): ?DailyFeedbackTeamPreferences;
}

<?php

namespace App\Core\Components\Feedback\Repository;

use App\Core\Components\Team\Entity\Team;
use App\Core\Components\Feedback\Entity\DailyFeedback;
use App\Core\Components\User\Entity\User;

interface DailyFeedbackRepositoryInterface
{
    public function add(DailyFeedback $entity, bool $flush = true): void;
    public function remove(DailyFeedback $entity, bool $flush = true): void;
    public function findLastDailyFeedbackByUser(User $user): ?DailyFeedback;
    public function findOneById($id): ?DailyFeedback;
    public function findLastWeekDailyFeedbackByTeam(Team $team): array;
}

<?php
namespace App\Core\Components\Feedback\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Feedback\Entity\PersonalFeedback;

interface PersonalFeedbackRepositoryInterface 
{
    public function add(PersonalFeedback $entity, bool $flush = true): void;
    public function remove(PersonalFeedback $entity, bool $flush = true): void;
    public function findOneById($id): ?PersonalFeedback;
    public function findByReceiver(User $receiver): ?PersonalFeedback;
    public function findLastWeekUserFeedbackByReceiver(User $receiver): array;
}
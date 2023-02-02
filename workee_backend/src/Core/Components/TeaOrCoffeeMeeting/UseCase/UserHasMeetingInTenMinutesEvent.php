<?php

namespace App\Core\Components\Feedback\UseCase;

use App\Core\Components\Team\Entity\Team;

final class UserHasMeetingInTenMinutesEvent
{
    public function __construct(
        private int $userId,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}

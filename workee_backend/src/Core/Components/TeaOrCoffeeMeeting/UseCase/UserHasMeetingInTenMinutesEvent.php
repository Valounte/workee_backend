<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

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

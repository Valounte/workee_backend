<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

final class UserHasMeetingInTenMinutesEvent
{
    public function __construct(
        public readonly int $initiatorId,
        public readonly array $invitedUsersIds,
        public readonly string $meetingName,
    ) {
    }
}

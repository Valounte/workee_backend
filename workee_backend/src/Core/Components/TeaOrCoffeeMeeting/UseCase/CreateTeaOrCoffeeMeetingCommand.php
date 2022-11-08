<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use DateTime;
use App\Core\Components\User\Entity\User;

final class CreateTeaOrCoffeeMeetingCommand
{
    public function __construct(
        private User $initiator,
        private array $invitedUsersIds,
        private DateTime $date,
    ) {
    }

    public function getInitiator(): User
    {
        return $this->initiator;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getInvitedUsersIds(): array
    {
        return $this->invitedUsersIds;
    }
}

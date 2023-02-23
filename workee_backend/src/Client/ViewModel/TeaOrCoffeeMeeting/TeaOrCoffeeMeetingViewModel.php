<?php

namespace App\Client\ViewModel\TeaOrCoffeeMeeting;

use DateTime;
use App\Client\ViewModel\User\UserViewModel;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;

final class TeaOrCoffeeMeetingViewModel
{
    public function __construct(
        private int $id,
        private TeaOrCoffeeMeetingUserViewModel $initiator,
        /** @var TeaOrCoffeeMeetingInvitedUserViewModel[] */
        private array $invitedUsersStatus,
        private TeaOrCoffeeMeetingTypeEnum $meetingType,
        private DateTime $date,
        private string $name,
    ) {
    }

    /**
     * Get the value of initiator
     */
    public function getInitiator()
    {
        return $this->initiator;
    }

    /**
     * Get the value of invitedUsers
     */
    public function getInvitedUsersStatus()
    {
        return $this->invitedUsersStatus;
    }

    /**
     * Get the value of meetingType
     */
    public function getMeetingType()
    {
        return $this->meetingType;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }
}

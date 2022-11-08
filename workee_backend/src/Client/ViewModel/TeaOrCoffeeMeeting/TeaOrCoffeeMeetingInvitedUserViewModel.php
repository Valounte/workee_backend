<?php

namespace App\Client\ViewModel\TeaOrCoffeeMeeting;

use App\Core\Components\User\Entity\User;
use App\Client\ViewModel\User\UserViewModel;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;

final class TeaOrCoffeeMeetingInvitedUserViewModel
{
    public function __construct(
        public TeaOrCoffeeMeetingUserViewModel $invitedUser,
        public InvitationStatusEnum $invitationStatus,
    ) {
    }

    /**
     * Get the value of invitedUser
     */
    public function getInvitedUser(): TeaOrCoffeeMeetingUserViewModel
    {
        return $this->invitedUser;
    }

    /**
     * Get the value of invitationStatus
     */
    public function getInvitationStatus(): InvitationStatusEnum
    {
        return $this->invitationStatus;
    }
}

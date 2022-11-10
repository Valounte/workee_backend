<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepository;

#[ORM\Entity(repositoryClass: TeaOrCoffeeMeetingUserRepository::class)]
class TeaOrCoffeeMeetingUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity:TeaOrCoffeeMeeting::class)]
    private $meeting;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $invitedUser;

    #[ORM\Column(type: 'invitation_status_type', length: 255)]
    private $invitationStatus;

    public function __construct(
        TeaOrCoffeeMeeting $meeting,
        User $invitedUser,
        InvitationStatusEnum $invitationStatus = InvitationStatusEnum::PENDING,
    ) {
        $this->invitedUser = $invitedUser;
        $this->meeting = $meeting;
        $this->invitationStatus = $invitationStatus;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of invitedUser
     */
    public function getInvitedUser()
    {
        return $this->invitedUser;
    }

    /**
     * Get the value of invitationStatus
     */
    public function getInvitationStatus()
    {
        return $this->invitationStatus;
    }

    /**
     * Get the value of meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * Set the value of invitationStatus
     *
     * @return  self
     */
    public function setInvitationStatus($invitationStatus)
    {
        $this->invitationStatus = $invitationStatus;

        return $this;
    }
}

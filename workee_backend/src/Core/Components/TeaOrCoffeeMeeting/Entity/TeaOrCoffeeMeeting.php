<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Entity;

use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepository;

#[ORM\Entity(repositoryClass: TeaOrCoffeeMeetingRepository::class)]
class TeaOrCoffeeMeeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $initiator;

    #[ORM\Column(type: 'tea_or_coffee_meeting_type', length: 255)]
    private $meetingType;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $date;

    public function __construct(
        User $initiator,
        DateTime $date,
        TeaOrCoffeeMeetingTypeEnum $meetingType,
        string $name,
    ) {
        $this->initiator = $initiator;
        $this->meetingType = $meetingType;
        $this->date = $date;
        $this->name = $name;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of initiator
     */
    public function getInitiator()
    {
        return $this->initiator;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the value of meetingType
     */
    public function getMeetingType()
    {
        return $this->meetingType;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }
}

<?php

namespace App\Core\Components\Notification\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use SpecShaper\EncryptBundle\Annotations\Encrypted;
use App\Infrastructure\Notification\Repository\NotificationRepository;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    /**
     * @phpstan-ignore-next-line
     * @Encrypted
     * @ORM\Column(type="string", length=255)
     */
    #[Encrypted]
    #[ORM\Column(type: 'string', length: 255)]
    private $message;

    #[ORM\ManyToOne(targetEntity:User::class)]
    private $sender;

    #[ORM\ManyToOne(targetEntity:User::class)]
    private $receiver;

    #[ORM\Column(type: 'notification_alert_level_type', length: 255)]
    private $alertLevel;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function __construct(
        string $message,
        User $sender,
        User $receiver,
        NotificationAlertLevelEnum $alertLevel,
        DateTime $created_at = new DateTime('now'),
    ) {
        $this->message = $message;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->alertLevel = $alertLevel;
        $this->created_at = $created_at;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the value of receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Get the value of sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Get the value of alertLevel
     */
    public function getAlertLevel()
    {
        return $this->alertLevel;
    }
}

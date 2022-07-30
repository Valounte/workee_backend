<?php

namespace App\Core\Components\Notification\ValueObject;

use App\Core\Components\Notification\ValueObject\Enum\NotificationAlertLevelEnum;
use DateTime;
use App\Core\Components\User\Entity\User;

final class Notification
{
    public function __construct(
        private User $sender,
        private User $recepteur,
        private string $message,
        private NotificationAlertLevelEnum $alertLevel,
        private DateTime $createdAt = new DateTime(),
    ) {
    }

    /**
     * Get the value of user
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the value of alertLevel
     */
    public function getAlertLevel()
    {
        return $this->alertLevel;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the value of recepteur
     */
    public function getRecepteur()
    {
        return $this->recepteur;
    }
}

<?php

namespace App\Client\ViewModel\Notification;

use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use DateTime;

final class NotificationViewModel
{
    public function __construct(
        private string $message,
        private string $senderFirstname,
        private string $senderLastname,
        private NotificationAlertLevelEnum $alertLevel,
        private DateTime $sentAt,
    ) {
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
     * Get the value of sentAt
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Get the value of senderFirstname
     */
    public function getSenderFirstname()
    {
        return $this->senderFirstname;
    }

    /**
     * Get the value of senderLastname
     */
    public function getSenderLastname()
    {
        return $this->senderLastname;
    }
}

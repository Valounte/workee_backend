<?php

namespace App\Client\ViewModel\Notification;

use App\Client\ViewModel\User\UserViewModel;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use DateTime;

final class NotificationPreferencesViewModel
{
    public function __construct(
        private int $id,
        private NotificationAlertLevelEnum $alertLevel,
        private bool $isMuted,
    ) {
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of alertLevel
     */
    public function getAlertLevel()
    {
        return $this->alertLevel;
    }

    /**
     * Get the value of isActivated
     */
    public function getIsMuted()
    {
        return $this->isMuted;
    }
}

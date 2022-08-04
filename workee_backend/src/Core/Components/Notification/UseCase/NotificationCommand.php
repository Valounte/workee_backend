<?php

namespace App\Core\Components\Notification\UseCase;

use App\Core\Components\Notification\ValueObject\Notification;

final class NotificationCommand
{
    public function __construct(
        private Notification $notification,
    ) {
    }

    /**
     * Get the value of notification
     */
    public function getNotification()
    {
        return $this->notification;
    }
}

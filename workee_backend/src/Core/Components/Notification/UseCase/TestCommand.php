<?php

namespace App\Core\Components\Notification\UseCase;

use App\Core\Components\Notification\Entity\Notification;

final class TestCommand
{
    public function __construct(
        private string $message,
    ) {
    }

    /**
     * Get the value of notification
     */
    public function getmessage(): string
    {
        return $this->message;
    }
}

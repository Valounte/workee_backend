<?php

namespace App\Client\ViewModel\Feedback;

use App\Client\ViewModel\User\PersonalFeedbackSenderViewModel;
use DateTime;

final class PersonalFeedbackViewModel
{
    public function __construct(
        private int $id,
        private PersonalFeedbackSenderViewModel $sender,
        private string $message,
        private DateTime $createdAt,
    ) {
    }

    /**
     * Get the value of sender
     */
    public function getSender(): PersonalFeedbackSenderViewModel
    {
        return $this->sender;
    }


    /**
     * Get the value of message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the value of created_at
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get the value of idFeedback
     */
    public function getId()
    {
        return $this->id;
    }
}

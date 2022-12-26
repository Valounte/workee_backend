<?php

namespace App\Client\ViewModel\User;

final class PersonalFeedbackSenderViewModel
{
    public function __construct(
        private string $firstname,
        private string $lastname,
    ) {
    }

    /**
     * Get the value of firstname
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }
}

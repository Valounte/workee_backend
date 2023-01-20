<?php

namespace App\Client\ViewModel\ProfessionalDevelopment;

final class ProfessionalDevelopmentUserViewModel
{
    public function __construct(
        private int $id,
        private string $firstname,
        private string $lastname,
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
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}

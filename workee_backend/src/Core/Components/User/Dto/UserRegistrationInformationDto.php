<?php

namespace App\Core\Components\User\Dto;

final class UserRegistrationInformationDto
{
    public function __construct(
        private string $email,
        private string $firstname,
        private string $lastname,
        private ?string $jobName = null,
        private ?string $teamname = null,
    ) {
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
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

    /**
     * Get the value of jobName
     */
    public function getJobName()
    {
        return $this->jobName;
    }

    /**
     * Get the value of teamname
     */
    public function getTeamname()
    {
        return $this->teamname;
    }
}

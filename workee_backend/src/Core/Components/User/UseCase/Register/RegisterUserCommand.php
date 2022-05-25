<?php

namespace App\Core\Components\User\UseCase\Register;

use App\Core\Components\Company\Entity\Company;

final class RegisterUserCommand
{
    public function __construct(
        private string $firstname,
        private string $lastname,
        private string $email,
        private Company $company,
        private ?string $password = null,
    ) {
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}

<?php

namespace App\Client\ViewModel;

use App\Core\Entity\User;
use App\Core\Entity\Company;

final class UserViewModel
{
    private string $email;

    private string $firstname;

    private string $lastname;

    private Company $company;

    private int $id;

    public function __construct(private User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
        $this->company = $user->getCompany();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getCompany(): string
    {
        return $this->company->getCompanyName();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }
}

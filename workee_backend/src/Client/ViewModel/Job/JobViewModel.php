<?php

namespace App\Client\ViewModel\Job;

use App\Client\ViewModel\Company\CompanyViewModel;

final class JobViewModel
{
    public function __construct(
        private int $id,
        private string $name,
        private string $description,
        private CompanyViewModel $company,
        private array $permissions,
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
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Get the value of permissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }
}

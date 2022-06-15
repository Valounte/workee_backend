<?php

namespace App\Client\ViewModel\Job;

use App\Client\ViewModel\Company\CompanyViewModel;

final class JobViewModel
{
    public function __construct(
        public int $id,
        public string $name,
        public CompanyViewModel $company,
        public array $permissions,
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
}

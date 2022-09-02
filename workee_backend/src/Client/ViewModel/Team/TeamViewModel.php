<?php

namespace App\Client\ViewModel\Team;

use App\Client\ViewModel\Company\CompanyViewModel;

final class TeamViewModel
{
    public function __construct(
        private int $id,
        private string $name,
        private ?CompanyViewModel $company = null,
    ) {
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
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }
}

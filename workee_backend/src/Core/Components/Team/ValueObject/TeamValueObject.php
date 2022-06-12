<?php

namespace App\Core\Components\Team\ValueObject;

use App\Core\Components\Company\Entity\Company;

final class TeamValueObject
{
    public function __construct(
        private string $name,
        private Company $company,
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
}

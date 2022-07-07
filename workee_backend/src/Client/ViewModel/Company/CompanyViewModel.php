<?php

namespace App\Client\ViewModel\Company;

final class CompanyViewModel
{
    public function __construct(
        private int $id,
        private string $name,
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
}

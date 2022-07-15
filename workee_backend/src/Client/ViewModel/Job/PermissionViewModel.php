<?php

namespace App\Client\ViewModel\Job;

use App\Core\Components\Job\Entity\Enum\PermissionContextEnum;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;

final class PermissionViewModel
{
    public function __construct(
        private int $id,
        private PermissionNameEnum $name,
        private PermissionContextEnum $context,
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
     * Get the value of context
     */
    public function getContext()
    {
        return $this->context;
    }
}

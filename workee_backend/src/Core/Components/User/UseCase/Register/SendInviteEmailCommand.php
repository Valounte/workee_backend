<?php

namespace App\Core\Components\User\UseCase\Register;

use App\Core\Components\User\Entity\User;

final class SendInviteEmailCommand
{
    public function __construct(
        private string $email,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

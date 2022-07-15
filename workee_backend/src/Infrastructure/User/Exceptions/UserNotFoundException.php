<?php

namespace App\Infrastructure\User\Exceptions;

use Throwable;

final class UserNotFoundException extends \Exception
{
    public function __construct(string $message = "User not found", int $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

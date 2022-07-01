<?php

namespace App\Infrastructure\User\Exceptions;

use Throwable;

final class UserPermissionsException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidTokenException(): self
    {
        return new self(
            "Invalid token",
            400
        );
    }

    public static function invalidJobPermissionException(): self
    {
        return new self(
            "Invalid job permission",
            400
        );
    }
}

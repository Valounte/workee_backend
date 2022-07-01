<?php

namespace App\Infrastructure\User\Exceptions;

use Throwable;

final class UserInformationException extends \Exception
{
    public function __construct(string $message = "", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function emailAlreadyUsedException(): self
    {
        return new self(
            "Email already used",
            400
        );
    }

    public static function invalidEmailException(): self
    {
        return new self(
            "Invalid email",
            400
        );
    }

    public static function invalidPasswordException(): self
    {
        return new self(
            "Invalid password format",
            400
        );
    }
}

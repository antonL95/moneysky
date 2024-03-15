<?php

declare(strict_types=1);

namespace App\Exceptions;

final class InvalidScopeException extends CustomAppException
{
    public static function invalidUserScope(): self
    {
        return new self('Invalid user scope', 403);
    }
}

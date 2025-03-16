<?php

declare(strict_types=1);

namespace App\Exceptions;

final class InvalidScopeExceptionAbstract extends AbstractAppException
{
    public static function invalidUserScope(): self
    {
        return new self('Invalid user scope', 403);
    }
}

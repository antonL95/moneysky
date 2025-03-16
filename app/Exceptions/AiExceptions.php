<?php

declare(strict_types=1);

namespace App\Exceptions;

final class AiExceptions extends AbstractAppException
{
    public static function invalidResponse(): self
    {
        return new self('Invalid response');
    }
}

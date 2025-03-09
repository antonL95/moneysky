<?php

declare(strict_types=1);

namespace App\Exceptions;

final class CovalentExceptions extends AbstractAppException
{
    public static function invalidApiConfig(): self
    {
        return new self('Covalent API URL or API key is not set', 500);
    }

    public static function invalidApiResponse(): self
    {
        return new self('Covalent API invalid response.', 500);
    }
}

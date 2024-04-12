<?php

declare(strict_types=1);

namespace App\Exceptions;

final class KrakenClientExceptions extends CustomAppException
{
    public static function errorResponse(string $error): self
    {
        return new self(
            sprintf('Error response from Kraken API: %s', $error),
            500,
        );
    }

    public static function invalidApiUrl(): self
    {
        return new self('Invalid Kraken API URL', 500);
    }
}

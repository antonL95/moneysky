<?php

declare(strict_types=1);

namespace App\Exceptions;

final class StockMarketClientException extends AbstractAppException
{
    public static function invalidResponseFromApi(string $message): self
    {
        return new self(
            sprintf('Invalid response from AlphaVantage API: %s', $message)
        );
    }

    public static function invalidValue(): self
    {
        return new self(
            'Invalid value'
        );
    }

    public static function invalidPriceCached(): self
    {
        return new self('Invalid price cached');
    }
}

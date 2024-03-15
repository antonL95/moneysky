<?php

declare(strict_types=1);

namespace App\MarketData\Exceptions;

use App\Exceptions\CustomAppException;

final class AlphaVantageClientException extends CustomAppException
{
    public static function invalidConfig(): self
    {
        return new self('AlphaVantage API URL or API Key is not set correctly');
    }

    public static function invalidResponseFromApi(string $message): self
    {
        return new self(
            sprintf('Invalid response from AlphaVantage API: %s', $message)
        );
    }

    public static function invalidPriceCached(): self
    {
        return new self('Invalid price cached');
    }
}

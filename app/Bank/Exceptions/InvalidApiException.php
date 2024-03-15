<?php

declare(strict_types=1);

namespace App\Bank\Exceptions;

use App\Exceptions\CustomAppException;

final class InvalidApiException extends CustomAppException
{
    public static function invalidConfiguration(): self
    {
        return new self(
            'Invalid configuration for the bank API',
            500,
        );
    }

    public static function createForAccountBalance(): self
    {
        return new self(
            'Invalid response from the bank API when fetching account balance',
            500,
        );
    }

    public static function noDataFound(): self
    {
        return new self(
            'No data found',
            500,
        );
    }

    public static function createForAccountTransactions(): self
    {
        return new self(
            'Invalid response from the bank API when fetching account transactions',
            500,
        );
    }

    public static function invalidDataEntry(string $object = ''): self
    {
        return new self(
            'Invalid data entry: '.$object,
            500,
        );
    }

    public static function invalidAccessToken(): self
    {
        return new self(
            'Invalid access token',
            500,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ErrorCodes;

final class InvalidApiExceptionAbstract extends AbstractAppException
{
    public static function invalidConfiguration(): self
    {
        return new self(
            'Invalid configuration for the bank API',
            500,
        );
    }

    public static function noDataFound(): self
    {
        return new self(
            'No data found',
            ErrorCodes::NO_DATA_FOUND->value,
        );
    }

    public static function accountIsNotReady(): self
    {
        return new self(
            'Account is not ready',
            500,
        );
    }
}

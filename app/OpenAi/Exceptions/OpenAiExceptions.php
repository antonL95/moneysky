<?php

declare(strict_types=1);

namespace App\OpenAi\Exceptions;

use App\Exceptions\CustomAppException;

class OpenAiExceptions extends CustomAppException
{
    public static function invalidConfiguration(): self
    {
        return new self('Invalid configuration');
    }

    public static function invalidData(): self
    {
        return new self('Invalid data');
    }
}

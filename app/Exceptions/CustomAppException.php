<?php

declare(strict_types=1);

namespace App\Exceptions;

class CustomAppException extends \Exception
{
    public static function invalidConfig(): self
    {
        return new self('Invalid configuration');
    }
}

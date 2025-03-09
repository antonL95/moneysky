<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

abstract class AbstractAppException extends Exception
{
    final public static function invalidConfig(): static
    {
        return new static('Invalid configuration'); // @phpstan-ignore-line
    }
}

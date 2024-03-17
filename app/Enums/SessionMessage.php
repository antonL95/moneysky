<?php

declare(strict_types=1);

namespace App\Enums;

enum SessionMessage: string
{
    case SUCCESS = 'successMessage';
    case ERROR = 'errorMessage';
    case INFO = 'infoMessage';
    case WARNING = 'warningMessage';
}

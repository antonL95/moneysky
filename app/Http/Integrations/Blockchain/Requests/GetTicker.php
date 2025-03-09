<?php

declare(strict_types=1);

namespace App\Http\Integrations\Blockchain\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetTicker extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
    ) {}

    public function resolveEndpoint(): string
    {
        return 'ticker';
    }
}

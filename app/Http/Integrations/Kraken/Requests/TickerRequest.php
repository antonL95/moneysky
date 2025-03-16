<?php

declare(strict_types=1);

namespace App\Http\Integrations\Kraken\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class TickerRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/0/public/Ticker';
    }
}

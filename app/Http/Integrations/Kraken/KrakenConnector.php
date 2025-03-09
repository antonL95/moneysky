<?php

declare(strict_types=1);

namespace App\Http\Integrations\Kraken;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class KrakenConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.kraken.com';
    }
}

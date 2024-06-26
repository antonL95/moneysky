<?php

declare(strict_types=1);

namespace App\Http\Integrations\Kraken;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class KrakenConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.kraken.com';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}

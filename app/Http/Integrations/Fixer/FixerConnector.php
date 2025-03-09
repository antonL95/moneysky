<?php

declare(strict_types=1);

namespace App\Http\Integrations\Fixer;

use Illuminate\Support\Facades\Config;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class FixerConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://data.fixer.io/api/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultAuth(): QueryAuthenticator
    {
        $apiKey = Config::string('services.fixer.api_key');

        return new QueryAuthenticator(
            'access_key',
            $apiKey,
        );
    }
}

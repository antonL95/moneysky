<?php

declare(strict_types=1);

namespace App\Http\Integrations\AlphaVantage;

use Illuminate\Support\Facades\Config;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class AlphaVantage extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://www.alphavantage.co/query';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultQuery(): array
    {
        return [
            'apikey' => Config::get('services.alpha_vantage.api_key'),
        ];
    }
}

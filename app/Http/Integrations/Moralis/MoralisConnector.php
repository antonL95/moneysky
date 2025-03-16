<?php

declare(strict_types=1);

namespace App\Http\Integrations\Moralis;

use App\Exceptions\MoralisExceptions;
use Illuminate\Support\Facades\Config;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class MoralisConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://deep-index.moralis.io/api/v2.2/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @throws MoralisExceptions
     */
    protected function defaultAuth(): HeaderAuthenticator
    {
        $apiKey = type(Config::get('services.moralis.api_key'))->asString();

        return new HeaderAuthenticator(
            $apiKey,
            'X-API-Key',
        );
    }
}

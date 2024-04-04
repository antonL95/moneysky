<?php

declare(strict_types=1);

namespace App\Http\Integrations\Covalent;

use App\Crypto\Exceptions\CovalentExceptions;
use Illuminate\Support\Facades\Config;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class CovalentConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.covalenthq.com';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @throws CovalentExceptions
     */
    protected function defaultAuth(): BasicAuthenticator
    {
        $apiKey = Config::get('covalent.api_key');
        if (!\is_string($apiKey)) {
            throw CovalentExceptions::invalidApiConfig();
        }

        return new BasicAuthenticator(
            $apiKey,
            '',
        );
    }
}

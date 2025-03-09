<?php

declare(strict_types=1);

namespace App\Http\Integrations\Blockchain;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

final class BlockchainConnector extends Connector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://blockchain.info/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}

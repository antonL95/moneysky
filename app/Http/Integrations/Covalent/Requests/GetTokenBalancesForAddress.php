<?php

declare(strict_types=1);

namespace App\Http\Integrations\Covalent\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetTokenBalancesForAddress extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $chainName,
        protected string $walletAddress,
    ) {
    }

    /**
     * @return array<string, string|bool>
     */
    protected function getDefaultQuery(): array
    {
        return [
            'quote-currency' => 'USD',
            'no-nft-fetch' => false,
            'no-spam' => true,
            'no-nft-asset-metadata' => false,
        ];
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/v1/%s/address/%s/balances_v2/',
            $this->chainName,
            $this->walletAddress,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\Moralis\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetTokenBalancesForAddress extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $chainName,
        private readonly string $walletAddress,
    ) {}

    public function resolveEndpoint(): string
    {
        return sprintf(
            'wallets/%s/tokens?chain=%s&exclude_spam=true',
            $this->walletAddress,
            $this->chainName,
        );
    }
}

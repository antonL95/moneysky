<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Accounts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * retrieve account balances
 *
 * Access account balances.
 *
 * Balances will be returned in Berlin Group PSD2 format.
 */
final class RetrieveAccountBalances extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/accounts/$this->id/balances/";
    }
}

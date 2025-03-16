<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Accounts;

use App\Data\GoCardless\AccountDetailsData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * retrieve account details
 *
 * Access account details.
 *
 * Account details will be returned in Berlin Group PSD2 format.
 */
final class RetrieveAccountDetails extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/accounts/$this->id/details/";
    }

    public function createDtoFromResponse(Response $response): AccountDetailsData
    {
        return AccountDetailsData::from($response->array()['account']);
    }
}

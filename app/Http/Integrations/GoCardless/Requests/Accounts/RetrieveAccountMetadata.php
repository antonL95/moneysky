<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Accounts;

use App\Data\GoCardless\AccountMetadataData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * retrieve account metadata
 *
 * Access account metadata.
 *
 * Information about the account record, such as the processing status and
 * IBAN.
 *
 * Account status is recalculated based on the error count in the latest req.
 */
final class RetrieveAccountMetadata extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/accounts/$this->id/";
    }

    public function createDtoFromResponse(Response $response): AccountMetadataData
    {
        return AccountMetadataData::from($response->array());
    }
}

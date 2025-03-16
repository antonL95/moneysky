<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Requisitions;

use App\Data\GoCardless\RequisitionAccountData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * requisition by id
 * Retrieve a requisition by ID
 */
final class RequisitionById extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  A UUID string identifying this requisition.
     */
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/requisitions/$this->id/";
    }

    public function createDtoFromResponse(Response $response): RequisitionAccountData
    {
        return RequisitionAccountData::from($response->array());
    }
}

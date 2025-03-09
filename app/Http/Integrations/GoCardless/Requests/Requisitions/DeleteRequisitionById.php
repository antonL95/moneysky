<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Requisitions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * delete requisition by id
 *
 * Delete requisition and its end user agreement
 */
final class DeleteRequisitionById extends Request
{
    protected Method $method = Method::DELETE;

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
}

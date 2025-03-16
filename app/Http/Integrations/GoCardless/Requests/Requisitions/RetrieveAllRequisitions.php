<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Requisitions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * retrieve all requisitions
 *
 * Retrieve all requisitions belonging to the company
 */
final class RetrieveAllRequisitions extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  null|int  $limit  Number of results to return per page.
     * @param  null|int  $offset  The initial zero-based index from which to return the results.
     */
    public function __construct(
        private readonly ?int $limit = null,
        private readonly ?int $offset = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/requisitions/';
    }

    public function defaultQuery(): array
    {
        return array_filter(['limit' => $this->limit, 'offset' => $this->offset]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Agreements;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * retrieve all EUAs for an end user
 *
 * API endpoints related to end-user agreements.
 */
final class RetrieveAllEuasForEndUser extends Request
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
        return '/api/v2/agreements/enduser/';
    }

    public function defaultQuery(): array
    {
        return array_filter(['limit' => $this->limit, 'offset' => $this->offset]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Agreements;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * retrieve EUA by id
 *
 * Retrieve end user agreement by ID
 */
final class RetrieveEuaById extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string  $id  A UUID string identifying this end user agreement.
     */
    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/agreements/enduser/$this->id/";
    }
}

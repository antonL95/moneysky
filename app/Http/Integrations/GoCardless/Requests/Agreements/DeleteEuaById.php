<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Agreements;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * delete EUA by id
 *
 * Delete an end user agreement
 */
final class DeleteEuaById extends Request
{
    protected Method $method = Method::DELETE;

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

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Institutions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * retrieve institution
 *
 * Get details about a specific Institution and its supported features
 */
final class RetrieveInstitution extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/institutions/$this->id/";
    }
}

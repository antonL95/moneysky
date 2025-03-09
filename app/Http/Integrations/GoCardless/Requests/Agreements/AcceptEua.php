<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Agreements;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class AcceptEua extends Request
{
    protected Method $method = Method::PUT;

    public function __construct(
        private readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/agreements/enduser/$this->id/accept/";
    }
}

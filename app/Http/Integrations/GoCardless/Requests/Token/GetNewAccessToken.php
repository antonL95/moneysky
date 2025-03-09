<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Token;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class GetNewAccessToken extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/token/refresh/';
    }
}

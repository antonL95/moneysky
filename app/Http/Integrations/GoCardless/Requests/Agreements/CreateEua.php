<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Agreements;

use App\Data\GoCardless\AgreementData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * create EUA
 * API endpoints related to end-user agreements.
 */
final class CreateEua extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private string $institutionId,
        private int $maxHistoricalDays = 90,
        private int $accessValidForDays = 90,
        /** @var string[] $accessScope */
        private array $accessScope = ['balances', 'details', 'transactions'],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/agreements/enduser/';
    }

    public function createDtoFromResponse(Response $response): AgreementData
    {
        return AgreementData::from($response->array());
    }

    /**
     * @return array<string, array<string>|int|string>
     */
    protected function defaultBody(): array
    {
        return [
            'institution_id' => $this->institutionId,
            'max_historical_days' => $this->maxHistoricalDays,
            'access_valid_for_days' => $this->accessValidForDays,
            'access_scope' => $this->accessScope,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\Fixer\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetLatestCurrencyRates extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  string[]  $currencies
     */
    public function __construct(
        private readonly string $baseCurrency,
        private readonly array $currencies,
    ) {}

    public function resolveEndpoint(): string
    {
        return 'latest';
    }

    protected function defaultQuery(): array
    {
        return ['base' => $this->baseCurrency, 'symbols' => implode(',', $this->currencies)];
    }
}

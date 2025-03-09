<?php

declare(strict_types=1);

namespace App\Http\Integrations\AlphaVantage\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class TimeSeriesDaily extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $ticker,
    ) {}

    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultQuery(): array
    {
        return [
            'function' => 'TIME_SERIES_DAILY',
            'symbol' => $this->ticker,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Accounts;

use App\Data\GoCardless\AccountTransactionsData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class RetrieveAccountTransactions extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $id,
        private readonly ?CarbonImmutable $dateFrom = null,
        private readonly ?CarbonImmutable $dateTo = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/v2/accounts/$this->id/transactions/";
    }

    public function defaultQuery(): array
    {
        $query = [];

        if ($this->dateFrom instanceof CarbonImmutable) {
            $query['date_from'] = $this->dateFrom->toDateString();
        }
        if ($this->dateTo instanceof CarbonImmutable) {
            $query['date_to'] = $this->dateTo->toDateString();
        }

        return $query;
    }

    /**
     * @return Collection<int, AccountTransactionsData>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $transactionsResponse = $response->array();

        return AccountTransactionsData::collect(
            (array) Arr::pull($transactionsResponse, 'transactions.booked'),
            Collection::class,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Resource;

use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountBalances;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountDetails;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountMetadata;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountTransactions;
use App\Http\Integrations\GoCardless\Resource;
use Carbon\CarbonImmutable;
use Saloon\Http\Response;

final class Accounts extends Resource
{
    public function retrieveAccountMetadata(string $id): Response
    {
        return $this->connector->send(new RetrieveAccountMetadata($id));
    }

    public function retrieveAccountBalances(string $id): Response
    {
        return $this->connector->send(new RetrieveAccountBalances($id));
    }

    public function retrieveAccountDetails(string $id): Response
    {
        return $this->connector->send(new RetrieveAccountDetails($id));
    }

    public function retrieveAccountTransactions(string $id, ?CarbonImmutable $dateFrom, ?CarbonImmutable $dateTo): Response
    {
        return $this->connector->send(new RetrieveAccountTransactions($id, $dateFrom, $dateTo));
    }
}

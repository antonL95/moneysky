<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless;

use App\Data\GoCardless\TokenData;
use App\Http\Integrations\GoCardless\Requests\Token\GetNewAccessToken;
use App\Http\Integrations\GoCardless\Requests\Token\ObtainNewAccessRefreshTokenPair;
use App\Http\Integrations\GoCardless\Resource\Accounts;
use App\Http\Integrations\GoCardless\Resource\Agreements;
use App\Http\Integrations\GoCardless\Resource\Institutions;
use App\Http\Integrations\GoCardless\Resource\Requisitions;
use Illuminate\Support\Facades\Config;
use Saloon\Contracts\Sender;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Senders\GuzzleSender;
use Saloon\Traits\Plugins\HasTimeout;

final class GoCardlessConnector extends Connector
{
    use HasTimeout;

    protected int $requestTimeout = 120;

    public function resolveBaseUrl(): string
    {
        return 'https://bankaccountdata.gocardless.com';
    }

    public function accounts(): Accounts
    {
        return new Accounts($this);
    }

    public function agreements(): Agreements
    {
        return new Agreements($this);
    }

    public function institutions(): Institutions
    {
        return new Institutions($this);
    }

    public function requisitions(): Requisitions
    {
        return new Requisitions($this);
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        if ($pendingRequest->getRequest() instanceof ObtainNewAccessRefreshTokenPair
            || $pendingRequest->getRequest() instanceof GetNewAccessToken) {
            return;
        }

        $secretKey = Config::string('services.gocardless.secret_key');
        $secretId = Config::string('services.gocardless.secret_id');

        /** @var TokenData $authResponse */
        $authResponse = $this
            ->send(new ObtainNewAccessRefreshTokenPair($secretId, $secretKey))
            ->dto();

        $pendingRequest->authenticate(new TokenAuthenticator($authResponse->access));
    }

    protected function defaultSender(): Sender
    {
        return resolve(GuzzleSender::class);
    }
}

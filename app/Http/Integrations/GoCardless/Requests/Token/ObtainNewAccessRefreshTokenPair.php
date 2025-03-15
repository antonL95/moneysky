<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Token;

use App\Data\GoCardless\TokenData;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

final class ObtainNewAccessRefreshTokenPair extends Request implements Cacheable, HasBody
{
    use HasCaching, HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $secretId,
        private readonly string $secretKey,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/token/new/';
    }

    public function createDtoFromResponse(Response $response): TokenData
    {
        return TokenData::from($response->array());
    }

    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store('redis'));
    }

    public function cacheExpiryInSeconds(): int
    {
        return 86400;
    }

    /**
     * @return array<string, string>
     */
    protected function defaultBody(): array
    {
        return [
            'secret_id' => $this->secretId,
            'secret_key' => $this->secretKey,
        ];
    }
}

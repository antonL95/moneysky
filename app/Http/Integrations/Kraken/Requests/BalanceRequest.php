<?php

declare(strict_types=1);

namespace App\Http\Integrations\Kraken\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Auth\MultiAuthenticator;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

final class BalanceRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    private readonly int $nonce;

    /**
     * @var array<string, int>
     */
    private readonly array $request;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $privateKey,
    ) {
        $this->nonce = (int) now()->timestamp;
        $this->request = [
            'nonce' => $this->nonce,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/0/private/Balance';
    }

    /**
     * @return array<string, int>
     */
    protected function defaultBody(): array
    {
        return $this->request;
    }

    protected function defaultAuth(): MultiAuthenticator
    {
        return new MultiAuthenticator(
            new HeaderAuthenticator($this->apiKey, 'API-Key'),
            new HeaderAuthenticator($this->signMessage(), 'API-Sign'),
        );
    }

    private function signMessage(): string
    {
        $message = http_build_query($this->request);
        $secret_buffer = base64_decode($this->privateKey);
        $hash = hash_init('sha256');
        hash_update($hash, $this->nonce.$message);
        $hash_digest = hash_final($hash, true);
        $hmac = hash_hmac('sha512', $this->resolveEndpoint().$hash_digest, $secret_buffer, true);

        return base64_encode($hmac);
    }
}

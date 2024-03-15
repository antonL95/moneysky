<?php

declare(strict_types=1);

namespace App\Crypto\Clients;

use App\Crypto\Contracts\ICryptoClient;
use App\Crypto\DataTransferObjects\CryptoQuoteDto;
use App\Crypto\Enums\ChainType;
use App\Crypto\Exceptions\CovalenthqClientExceptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CovalenthqClient implements ICryptoClient
{
    protected string $apiUrl;

    protected string $apiKey;

    /**
     * @throws CovalenthqClientExceptions
     */
    public function __construct()
    {
        $apiUrl = Config::get('services.covalenthq.url');
        $apiKey = Config::get('services.covalenthq.apiKey');

        if (!\is_string($apiUrl) || !\is_string($apiKey)) {
            throw CovalenthqClientExceptions::invalidApiConfig();
        }

        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
    }

    /**
     * @return Collection<int, CryptoQuoteDto>
     *
     * @throws CovalenthqClientExceptions
     */
    public function fetchTokenQuotes(
        ChainType $chainType,
        string $walletAddress,
    ): Collection {
        $quoteEndpoint = sprintf(
            '%s/v1/%s/address/%s/balances_v2/',
            $this->apiUrl,
            $chainType->getChainName(),
            $walletAddress,
        );

        $response = (array) Http::withBasicAuth(
            $this->apiKey,
            '',
        )->get($quoteEndpoint, [
            'quote-currency' => 'USD',
            'no-nft-fetch' => false,
            'no-spam' => true,
            'no-nft-asset-metadata' => false,
        ])->json();

        if (!isset($response['data'])) {
            throw CovalenthqClientExceptions::invalidApiResponse();
        }

        $data = $response['data'];

        if (!\is_array($data) || !isset($data['items'])) {
            throw CovalenthqClientExceptions::invalidApiResponse();
        }

        $items = $data['items'];

        if (!\is_array($items)) {
            throw CovalenthqClientExceptions::invalidApiResponse();
        }

        $temp = [];

        foreach ($items as $currency) {
            if ($currency['quote'] < 1) {
                continue;
            }

            $temp[] = new CryptoQuoteDto(
                (string) $currency['contract_display_name'],
                (int) floor($currency['quote'] * 100),
            );
        }

        return collect($temp);
    }
}

<?php

declare(strict_types=1);

namespace App\Crypto\Contracts;

use App\Crypto\DataTransferObjects\CryptoQuoteDto;
use App\Crypto\Enums\ChainType;
use Illuminate\Support\Collection;

interface ICryptoClient
{
    /**
     * @return Collection<int, CryptoQuoteDto>
     */
    public function fetchTokenQuotes(
        ChainType $chainType,
        string $walletAddress,
    ): Collection;
}

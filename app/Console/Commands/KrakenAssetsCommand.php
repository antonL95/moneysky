<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CryptoExchangeService;
use Illuminate\Console\Command;

final class KrakenAssetsCommand extends Command
{
    protected $signature = 'app:kraken-assets';

    protected $description = 'Download trade-able ticker pairs and trade values from Kraken API.';

    public function handle(
        CryptoExchangeService $krakenService,
    ): int {

        $savedPairs = $krakenService->saveTickerPairs();

        $this->info('Downloaded '.$savedPairs.' pairs');

        return 0;
    }
}

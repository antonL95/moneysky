<?php

declare(strict_types=1);

namespace App\Console;

use App\Services\KrakenService;
use Illuminate\Console\Command;

class KrakenAssetsCommand extends Command
{
    protected $signature = 'app:kraken-assets';

    protected $description = 'Download trade-able ticker pairs and trade values from Kraken API.';

    public function handle(
        KrakenService $krakenService,
    ): int {

        $savedPairs = $krakenService->saveTickerPairs();

        $this->info('Downloaded '.$savedPairs.' pairs');

        return 0;
    }
}

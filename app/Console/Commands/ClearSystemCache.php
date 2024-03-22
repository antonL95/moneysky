<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearSystemCache extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:clear-system-cache';

    /**
     * @var string
     */
    protected $description = 'Clear php-fpm, opcache, and config cache.';

    public function handle(): void
    {
        $this->info('Clearing php-fpm cache...');
        opcache_reset();

        $this->info('Clearing config cache...');
        Artisan::call('config:clear');

        $this->info('System cache cleared.');
    }
}

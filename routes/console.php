<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command(
    'app:create-budgets',
)->monthlyOn();

Schedule::command(
    'app:download-institutions',
)->daily();

Schedule::command(
    'queue:prune-batches',
)->daily();

Schedule::command(
    'app:kraken-assets',
)->everyTenMinutes();

Schedule::command(
    'app:calculate-budgets',
)->everyFifteenMinutes();

Schedule::command(
    'app:process-stock-market',
)->daily();

Schedule::command(
    'app:process-bank-accounts',
)->everySixHours();

Schedule::command(
    'app:process-crypto-wallets',
)->hourly();

Schedule::command(
    'app:process-kraken-accounts',
)->everyFifteenMinutes();

Schedule::command(
    'app:aggregate-transactions',
)->everyFifteenMinutes();

Schedule::command(
    'app:process-transactions',
)->everyMinute();

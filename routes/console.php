<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Bank
Schedule::command(
    'app:get-bank-transactions'
)->hourly();

Schedule::command(
    'app:process-transactions'
)->everyMinute();

Schedule::command(
    'app:download-institutions'
)->daily();

Schedule::command(
    'app:check-requisition-accounts'
)->twiceMonthly();

// Crypto
Schedule::command(
    'app:kraken-account-balance'
)->hourly();

Schedule::command(
    'app:kraken-assets'
)->daily();

Schedule::command(
    'app:wallets-balance'
)->hourly();

// Market Data
Schedule::command(
    'app:stock-market-data'
)->hourly();

<?php

declare(strict_types=1);

use App\Actions\CalculateSnapshotBalances;
use App\Enums\AssetType;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserCryptoWallet;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use App\Models\UserStockMarket;
use Carbon\CarbonImmutable;

it('calculates snapshot balances', function () {
    $user = User::factory()->create();

    UserCryptoWallet::factory()->create([
        'user_id' => $user->id,
    ]);

    UserStockMarket::factory()->create([
        'user_id' => $user->id,
    ]);

    UserKrakenAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    UserManualEntry::factory()->create([
        'user_id' => $user->id,
    ]);

    UserBankAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    $snapshot = UserPortfolioSnapshot::factory()->create([
        'user_id' => $user->id,
    ]);

    $calculateSnapshotBalances = new CalculateSnapshotBalances;

    $calculateSnapshotBalances->handle($snapshot, $user);

    $sum = UserCryptoWallet::getSumOfUserWallets($user, true)
        + UserStockMarket::getSumOfAllTickers($user, true)
        + UserKrakenAccount::getSumOfAllAccounts($user, true)
        + UserBankAccount::getSumOfAllUserBankAccounts($user, true)
        + UserManualEntry::getSumWithCurrency($user, true);

    expect($snapshot->balance_cents)->toBe($sum)
        ->and($snapshot->change)->toBe(0.0);
});

it('does not fail when dividing by zero', function () {
    $user = User::factory()->create();

    UserCryptoWallet::factory()->create([
        'user_id' => $user->id,
    ]);

    UserCryptoWallet::factory()->create([
        'user_id' => $user->id,
    ]);

    UserStockMarket::factory()->create([
        'user_id' => $user->id,
    ]);

    UserKrakenAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    UserManualEntry::factory()->create([
        'user_id' => $user->id,
    ]);

    UserBankAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    $snapshot = UserPortfolioSnapshot::factory()->create([
        'user_id' => $user->id,
    ]);

    $previousSnapshot = UserPortfolioSnapshot::factory()->create([
        'user_id' => $user->id,
        'balance_cents' => 0,
        'aggregate_date' => CarbonImmutable::now()->subDay()->toDateString(),
    ]);

    UserPortfolioAsset::factory()->create([
        'user_id' => $user->id,
        'balance_cents' => 0,
        'snapshot_id' => $previousSnapshot->id,
        'asset_type' => AssetType::CRYPTO->value,
    ]);

    $calculateSnapshotBalances = new CalculateSnapshotBalances;

    $calculateSnapshotBalances->handle($snapshot, $user);

    $sum = UserCryptoWallet::getSumOfUserWallets($user, true)
        + UserStockMarket::getSumOfAllTickers($user, true)
        + UserKrakenAccount::getSumOfAllAccounts($user, true)
        + UserBankAccount::getSumOfAllUserBankAccounts($user, true)
        + UserManualEntry::getSumWithCurrency($user, true);

    expect($snapshot->balance_cents)->toBe($sum)
        ->and($snapshot->change)->toBe(0.0);
});

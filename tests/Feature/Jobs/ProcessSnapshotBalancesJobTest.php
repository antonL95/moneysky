<?php

declare(strict_types=1);

use App\Actions\CalculateSnapshotBalances;
use App\Enums\AssetType;
use App\Jobs\ProcessSnapshotBalancesJob;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserCryptoWallet;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use App\Models\UserStockMarket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create a snapshot
    $this->snapshot = UserPortfolioSnapshot::create([
        'user_id' => $this->user->id,
        'aggregate_date' => now(),
        'balance_cents' => 0,
        'change' => 0,
    ]);
});

it('calculates and updates snapshot balances for all asset types', function () {
    // Create test data for each asset type
    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 1000,
    ]);

    $stockMarket = UserStockMarket::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 2000,
        'amount' => 1,
    ]);

    $cryptoWallet = UserCryptoWallet::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 3000,
    ]);

    $krakenAccount = UserKrakenAccount::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 4000,
    ]);

    $manualEntry = UserManualEntry::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 5000,
    ]);

    // Create and dispatch the job
    $job = new ProcessSnapshotBalancesJob($this->snapshot, $this->user);
    $job->handle(app(CalculateSnapshotBalances::class));

    // Assert that asset snapshots were created for each asset type
    expect(UserPortfolioAsset::withoutGlobalScopes()->count())->toBe(5);

    // Assert bank accounts snapshot
    $bankSnapshot = UserPortfolioAsset::withoutGlobalScopes()
        ->where('asset_type', AssetType::BANK_ACCOUNTS->value)
        ->where('snapshot_id', $this->snapshot->id)
        ->first();

    expect($bankSnapshot)->not->toBeNull()
        ->and($bankSnapshot->balance_cents)->toBe(1000);

    // Assert stock market snapshot
    $stockSnapshot = UserPortfolioAsset::withoutGlobalScopes()
        ->where('asset_type', AssetType::STOCK_MARKET->value)
        ->where('snapshot_id', $this->snapshot->id)
        ->first();

    expect($stockSnapshot)->not->toBeNull()
        ->and($stockSnapshot->balance_cents)->toBe(2000);

    // Assert crypto snapshot
    $cryptoSnapshot = UserPortfolioAsset::withoutGlobalScopes()
        ->where('asset_type', AssetType::CRYPTO->value)
        ->where('snapshot_id', $this->snapshot->id)
        ->first();
    expect($cryptoSnapshot)->not->toBeNull()
        ->and($cryptoSnapshot->balance_cents)->toBe(3000);

    // Assert exchange snapshot
    $exchangeSnapshot = UserPortfolioAsset::withoutGlobalScopes()
        ->where('asset_type', AssetType::EXCHANGE->value)
        ->where('snapshot_id', $this->snapshot->id)
        ->first();
    expect($exchangeSnapshot)->not->toBeNull()
        ->and($exchangeSnapshot->balance_cents)->toBe(4000);

    // Assert manual entries snapshot
    $manualSnapshot = UserPortfolioAsset::withoutGlobalScopes()
        ->where('asset_type', AssetType::MANUAL_ENTRIES->value)
        ->where('snapshot_id', $this->snapshot->id)
        ->first();
    expect($manualSnapshot)->not->toBeNull()
        ->and($manualSnapshot->balance_cents)->toBe(5000)
        ->and($this->snapshot->fresh()->balance_cents)->toBe(15000);
});

it('handles case when user has no assets', function () {
    // Create and dispatch the job
    $job = new ProcessSnapshotBalancesJob($this->snapshot, $this->user);
    $job->handle(app(CalculateSnapshotBalances::class));

    // Assert that asset snapshots were created with zero balances
    expect(UserPortfolioAsset::withoutGlobalScopes()->count())->toBe(5);

    foreach (AssetType::cases() as $assetType) {
        $snapshot = UserPortfolioAsset::withoutGlobalScopes()
            ->where('asset_type', $assetType->value)
            ->where('snapshot_id', $this->snapshot->id)
            ->first();
        expect($snapshot)->not->toBeNull()
            ->and($snapshot->balance_cents)->toBe(0);
    }

    // Assert total balance in portfolio snapshot
    expect($this->snapshot->fresh()->balance_cents)->toBe(0);
});

it('updates existing asset snapshots instead of creating new ones', function () {
    // Create an existing asset snapshot
    $existingSnapshot = UserPortfolioAsset::create([
        'user_id' => $this->user->id,
        'snapshot_id' => $this->snapshot->id,
        'asset_type' => AssetType::BANK_ACCOUNTS->value,
        'balance_cents' => 1000,
        'change' => 100,
    ]);

    // Create a bank account with new balance
    UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'balance_cents' => 2000,
    ]);

    // Create and dispatch the job
    $job = new ProcessSnapshotBalancesJob($this->snapshot, $this->user);
    $job->handle(app(CalculateSnapshotBalances::class));

    // Assert that the existing snapshot was updated
    expect($existingSnapshot->fresh()->balance_cents)->toBe(2000);
});

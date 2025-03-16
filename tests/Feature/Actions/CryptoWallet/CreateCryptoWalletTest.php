<?php

declare(strict_types=1);

use App\Actions\CryptoWallet\CreateCryptoWallet;
use App\Data\App\CryptoWallet\CryptoWalletData;
use App\Enums\ChainType;
use App\Exceptions\InvalidScopeExceptionAbstract;
use App\Jobs\ProcessCryptoWalletsJob;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

it('successfully updates', function () {
    Illuminate\Support\Facades\Queue::fake();
    $user = App\Models\User::factory()->create();

    assertDatabaseCount('user_crypto_wallets', 0);
    actingAs($user);

    app(CreateCryptoWallet::class)->handle(
        $user,
        new CryptoWalletData(
            '0x123',
            ChainType::MATIC,
        ),
    );
    assertDatabaseCount('user_crypto_wallets', 1);

    expect($user->userCryptoWallet()->first()?->fresh()->wallet_address)
        ->toBe('0x123')
        ->and($user->userCryptoWallet()->first()?->fresh()->chain_type)
        ->toBe(ChainType::MATIC);

    Illuminate\Support\Facades\Queue::assertPushed(ProcessCryptoWalletsJob::class);
});

it('throws invalid scope', function () {
    Illuminate\Support\Facades\Queue::fake();
    $user = App\Models\User::factory()->create();

    assertDatabaseCount('user_crypto_wallets', 0);
    app(CreateCryptoWallet::class)->handle(
        $user,
        new CryptoWalletData(
            '0x123',
            ChainType::MATIC,
        ),
    );
    assertDatabaseCount('user_crypto_wallets', 1);

    expect(static fn () => $user->userCryptoWallet()->first()?->fresh()->wallet_address)
        ->toThrow(InvalidScopeExceptionAbstract::class);

    Illuminate\Support\Facades\Queue::assertPushed(ProcessCryptoWalletsJob::class);
});

<?php

declare(strict_types=1);

use App\Actions\CryptoWallet\UpdateCryptoWallet;
use App\Data\App\CryptoWallet\CryptoWalletData;
use App\Enums\ChainType;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\UserCryptoWallet;

use function Pest\Laravel\assertDatabaseHas;

it('successfully updates', function () {
    Illuminate\Support\Facades\Queue::fake();
    $cryptoWallet = UserCryptoWallet::factory()->create();

    assertDatabaseHas('user_crypto_wallets', [
        'id' => $cryptoWallet->id,
        'wallet_address' => $cryptoWallet->wallet_address,
    ]);

    app(UpdateCryptoWallet::class)->handle(
        $cryptoWallet,
        new CryptoWalletData(
            $cryptoWallet->wallet_address,
            ChainType::MATIC,
        ),
    );
    expect($cryptoWallet->fresh()->wallet_address)
        ->toBe($cryptoWallet->wallet_address)
        ->and($cryptoWallet->fresh()->chain_type)
        ->toBe(ChainType::MATIC);

    Illuminate\Support\Facades\Queue::assertPushed(ProcessCryptoWalletsJob::class);
});

<?php

declare(strict_types=1);

use App\Actions\CryptoWallet\DeleteCryptoWallet;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserCryptoWallet;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('successfully updates', function () {
    Illuminate\Support\Facades\Queue::fake();
    $cryptoWallet = UserCryptoWallet::factory()->create();

    assertDatabaseHas('user_crypto_wallets', [
        'id' => $cryptoWallet->id,
        'wallet_address' => $cryptoWallet->wallet_address,
    ]);

    app(DeleteCryptoWallet::class)->handle(
        $cryptoWallet,
    );

    assertDatabaseMissing('user_crypto_wallets', [
        'id' => $cryptoWallet->id,
        'wallet_address' => $cryptoWallet->wallet_address,
    ]);

    Illuminate\Support\Facades\Queue::assertPushed(ProcessSnapshotJob::class);
});

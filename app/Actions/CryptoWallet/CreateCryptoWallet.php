<?php

declare(strict_types=1);

namespace App\Actions\CryptoWallet;

use App\Data\App\CryptoWallet\CryptoWalletData;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\User;

final readonly class CreateCryptoWallet
{
    public function handle(User $user, CryptoWalletData $data): void
    {
        $userCryptoWallet = $user->userCryptoWallet()->create([
            'wallet_address' => $data->address,
            'chain_type' => $data->chainType->value,
        ]);

        ProcessCryptoWalletsJob::dispatch($userCryptoWallet);
    }
}

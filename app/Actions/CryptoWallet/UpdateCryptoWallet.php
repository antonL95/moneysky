<?php

declare(strict_types=1);

namespace App\Actions\CryptoWallet;

use App\Data\App\CryptoWallet\CryptoWalletData;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Models\UserCryptoWallet;

final readonly class UpdateCryptoWallet
{
    public function handle(UserCryptoWallet $userCryptoWallet, CryptoWalletData $data): void
    {
        $userCryptoWallet->update([
            'wallet_address' => $data->address,
            'chain_type' => $data->chainType,
        ]);

        ProcessCryptoWalletsJob::dispatch($userCryptoWallet);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Crypto\Enums\ChainType;
use App\Crypto\Jobs\ProcessCryptoWallets;
use App\Crypto\Models\UserCryptoWallets;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Rule;
use Livewire\Form;

class CryptoWalletForm extends Form
{
    #[Rule(['required', 'string'])]
    public ?string $wallet_address;

    #[Rule(['required', new Enum(ChainType::class)])]
    public ?string $chain_type;

    public function store(): void
    {
        $this->validate();

        $wallet = UserCryptoWallets::create([
            'user_id' => auth()->id(),
            'wallet_address' => $this->wallet_address,
            'chain_type' => $this->chain_type,
        ]);

        ProcessCryptoWallets::dispatch($wallet);
    }

    public function update(UserCryptoWallets $cryptoWallets): void
    {
        $this->validate();

        $cryptoWallets->update([
            'wallet_address' => $this->wallet_address,
            'chain_type' => $this->chain_type,
        ]);

        ProcessCryptoWallets::dispatch($cryptoWallets);
    }
}

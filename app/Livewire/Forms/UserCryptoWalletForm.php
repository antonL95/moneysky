<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\ChainType;
use App\Jobs\ProcessCryptoWallets;
use App\Models\UserCryptoWallets;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserCryptoWalletForm extends Form
{
    #[Validate(['required', 'string'])]
    public ?string $wallet_address;

    #[Validate(['required', new Enum(ChainType::class)])]
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

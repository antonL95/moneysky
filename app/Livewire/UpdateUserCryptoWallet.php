<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Enums\ChainType;
use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\Forms\UserCryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class UpdateUserCryptoWallet extends Component
{
    use Interactions;

    public UserCryptoWalletForm $form;

    public UserCryptoWallets $wallet;

    /**
     * @var array<int, array{id: string, name: string}>
     */
    public array $chainTypes;

    public function mount(UserCryptoWallets $wallet): void
    {
        $this->form->wallet_address = $wallet->wallet_address;
        $this->form->chain_type = $wallet->chain_type->value;
        $chainTypes = [];

        foreach (ChainType::cases() as $chainType) {
            $chainTypes[] = [
                'id' => $chainType->value,
                'name' => $chainType->value,
            ];
        }

        $this->chainTypes = $chainTypes;
    }

    public function update(UserCryptoWallets $wallet): void
    {
        $this->form->update($wallet);

        $this->dispatch('crypto-updated');
        $this->dispatch('close');
    }

    public function render(): View
    {
        return view('livewire.user-crypto-wallets.update-user-crypto-wallet');
    }
}

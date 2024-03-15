<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserCryptoWallets;
use App\Livewire\Forms\CryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditCryptoWallet extends Component
{
    public CryptoWalletForm $form;

    public UserCryptoWallets $wallet;

    public function mount(UserCryptoWallets $wallet): void
    {
        $this->form->wallet_address = $wallet->wallet_address;
        $this->form->chain_type = $wallet->chain_type->value;
    }

    public function update(UserCryptoWallets $wallet): void
    {
        $this->form->update($wallet);

        $this->dispatch('wallet-updated');
        $this->redirect(route('app.crypto-wallets'), true);
    }

    public function render(): View
    {
        return view('livewire.edit-crypto-wallet');
    }
}

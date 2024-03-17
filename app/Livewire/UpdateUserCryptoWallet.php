<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserCryptoWallets;
use App\Enums\SessionMessage;
use App\Livewire\Forms\UserCryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpdateUserCryptoWallet extends Component
{
    public UserCryptoWalletForm $form;

    public UserCryptoWallets $wallet;

    public function mount(UserCryptoWallets $wallet): void
    {
        $this->form->wallet_address = $wallet->wallet_address;
        $this->form->chain_type = $wallet->chain_type->value;
    }

    public function update(UserCryptoWallets $wallet): void
    {
        $this->form->update($wallet);

        session()->put(SessionMessage::SUCCESS->value, 'Crypto Wallet updated!');
        $this->redirect(route('app.crypto-wallets'), true);
    }

    public function render(): View
    {
        return view('livewire.update-user-crypto-wallet');
    }
}

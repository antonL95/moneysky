<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Forms\UserCryptoWalletForm;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddUserCryptoWallet extends Component
{
    public UserCryptoWalletForm $form;

    public function create(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        $this->form->store();

        $this->dispatch('wallet-created');
        $this->redirect(route('app.crypto-wallets'), true);
    }

    public function render(): View
    {
        return view('livewire.add-user-crypto-wallet');
    }
}

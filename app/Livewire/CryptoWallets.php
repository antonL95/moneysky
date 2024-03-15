<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserCryptoWallets;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CryptoWallets extends Component
{
    use WithPagination;

    public string $userCurrency;

    public int $walletsTotal;

    public function mount(): void
    {
        $userSettings = auth()->user()?->settings()?->whereKey('currency')?->first();

        $this->userCurrency = $userSettings->currency ?? 'USD';
        $sum = UserCryptoWallets::sum('balance_cents');
        if (!is_numeric($sum)) {
            $this->walletsTotal = 0;
        } else {
            $this->walletsTotal = (int) $sum;
        }
    }

    public function delete(UserCryptoWallets $wallet): void
    {
        $wallet->delete();

        $this->dispatch('wallet-deleted');
    }

    #[On('wallet-updated')]
    #[On('wallet-created')]
    #[On('wallet-deleted')]
    public function render(): View
    {
        return view('livewire.crypto-wallets', [
            'cryptoWallets' => UserCryptoWallets::paginate(10),
        ]);
    }
}

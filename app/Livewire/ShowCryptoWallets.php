<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserCryptoWallets;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ShowCryptoWallets extends Component
{
    use Toast;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserCryptoWallets $wallet): void
    {
        $wallet->delete();

        $this->dispatch('wallet-deleted');
        $this->success('Kraken account deleted!');
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserCryptoWallets>>
     */
    public function with(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'wallet_address', 'label' => 'Wallet Address'],
            ['key' => 'chain_type', 'label' => 'Chain Type'],
            ['key' => 'balance_cents', 'label' => 'Balance'],
        ];

        $rows = UserCryptoWallets::orderBy(...array_values($this->sortBy))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    public function render(): View
    {
        return view('livewire.user-crypto-wallets.show-crypto-wallets', $this->with());
    }
}

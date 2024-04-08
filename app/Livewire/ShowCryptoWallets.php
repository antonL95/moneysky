<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserCryptoWallets;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowCryptoWallets extends Component
{
    use Interactions;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserCryptoWallets $wallet): void
    {
        $wallet->delete();

        $this->dispatch('crypto-deleted');
        $this->toast()->success('Wallet deleted!', 'Wallet deleted successfully')->send();
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserCryptoWallets>>
     */
    #[On('crypto-added')]
    #[On('crypto-deleted')]
    #[On('crypto-updated')]
    public function with(): array
    {
        $headers = [
            ['index' => 'id', 'label' => '#'],
            ['index' => 'wallet_address', 'label' => 'Wallet Address'],
            ['index' => 'chain_type', 'label' => 'Chain Type'],
            ['index' => 'balance_cents', 'label' => 'Balance'],
            ['index' => 'action'],
        ];

        $rows = UserCryptoWallets::orderBy(...array_values($this->sort))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('crypto-added')]
    public function added(): void
    {
        $this->toast()->success('Wallet added!', 'Wallet added successfully!')->send();
    }

    #[On('crypto-updated')]
    public function updated(): void
    {
        $this->toast()->success('Wallet updated!', 'Wallet updated successfully!')->send();
    }

    public function render(): View
    {
        return view('livewire.user-crypto-wallets.show-crypto-wallets', $this->with());
    }
}

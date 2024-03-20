<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserKrakenAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ShowKrakenAccounts extends Component
{
    use Toast;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserKrakenAccount $krakenAccount): void
    {
        $krakenAccount->delete();

        $this->dispatch('kraken-account-deleted');
        $this->success('Kraken account deleted!');
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserKrakenAccount>>
     */
    public function with(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'api_key', 'label' => 'Api key'],
            ['key' => 'balance_cents', 'label' => 'Balance'],
        ];

        $rows = UserKrakenAccount::orderBy(...array_values($this->sortBy))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('kraken-account-deleted')]
    public function render(): View
    {
        return view('livewire.user-kraken-account.show-kraken-accounts', $this->with());
    }
}

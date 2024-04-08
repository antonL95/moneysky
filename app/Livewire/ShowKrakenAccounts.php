<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserKrakenAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowKrakenAccounts extends Component
{
    use Interactions;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserKrakenAccount $krakenAccount): void
    {
        $krakenAccount->delete();

        $this->dispatch('kraken-deleted');
        $this->toast()->success('Kraken account deleted!', 'Kraken account deleted successfully.!')->send();
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserKrakenAccount>>
     */
    #[On('kraken-added')]
    #[On('kraken-updated')]
    #[On('kraken-deleted')]
    public function with(): array
    {
        $headers = [
            ['index' => 'id', 'label' => '#'],
            ['index' => 'api_key', 'label' => 'Api key'],
            ['index' => 'balance_cents', 'label' => 'Balance'],
            ['index' => 'action'],
        ];

        $rows = UserKrakenAccount::orderBy(...array_values($this->sort))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('kraken-added')]
    public function added(): void
    {
        $this->toast()->success('Kraken account added!', 'Kraken account added successfully!')->send();
    }

    #[On('kraken-updated')]
    public function updated(): void
    {
        $this->toast()->success('Kraken account updated!', 'Kraken account updated successfully!')->send();
    }

    #[On('kraken-account-deleted')]
    public function render(): View
    {
        return view('livewire.user-kraken-account.show-kraken-accounts', $this->with());
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire;

use App\ManualEntry\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowManualEntries extends Component
{
    use Interactions;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserManualEntry $wallet): void
    {
        $wallet->delete();

        $this->dispatch('cash-deleted');
        $this->toast()->success('Cash wallet deleted!', 'Cash wallet deleted successfully!')->send();
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserManualEntry>>
     */
    #[On('cash-added')]
    #[On('cash-updated')]
    #[On('cash-deleted')]
    public function with(): array
    {
        $headers = [
            ['index' => 'id', 'label' => '#'],
            ['index' => 'name', 'label' => 'Name'],
            ['index' => 'amount_cents', 'label' => 'Amount'],
            ['index' => 'currency', 'label' => 'Currency'],
            ['index' => 'action'],
        ];

        $rows = UserManualEntry::orderBy(...array_values($this->sort))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('cash-added')]
    public function added(): void
    {
        $this->toast()->success('Cash wallet added!', 'Cash wallet added successfully!')->send();
    }

    #[On('cash-updated')]
    public function updated(): void
    {
        $this->toast()->success('Cash wallet updated!', 'Cash wallet updated successfully!')->send();
    }

    public function render(): View
    {
        return view(
            'livewire.user-manual-entries.show-manual-entries',
            $this->with(),
        );
    }
}

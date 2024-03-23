<?php

declare(strict_types=1);

namespace App\Livewire;

use App\ManualEntry\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class ShowManualEntries extends Component
{
    use Toast;
    use WithPagination;

    /**
     * @var string[]
     */
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

    public function delete(UserManualEntry $wallet): void
    {
        $wallet->delete();

        $this->dispatch('user-manual-entry-deleted');
        $this->success('Cash wallet deleted successfully');
    }

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserManualEntry>>
     */
    public function with(): array
    {
        $headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'amount_cents', 'label' => 'Amount'],
            ['key' => 'currency', 'label' => 'Currency'],
        ];

        $rows = UserManualEntry::orderBy(...array_values($this->sortBy))->paginate(10);

        return [
            'headers' => $headers,
            'rows' => $rows,
        ];
    }

    #[On('user-manual-entry-deleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view(
            'livewire.user-manual-entries.show-manual-entries', $this->with(),
        );
    }
}

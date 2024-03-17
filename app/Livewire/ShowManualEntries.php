<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\UserManualEntry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowManualEntries extends Component
{
    use Interactions;
    use WithPagination;

    public function delete(UserManualEntry $wallet): void
    {
        $wallet->delete();

        $this->dispatch('userManualEntryDeleted');
        $this->toast()->success('Cash wallet deleted successfully')->send();
    }

    #[On('userManualEntryAdded')]
    public function added(): void
    {
        $this->toast()->success('Cash wallet added successfully')->send();
    }

    #[On('userManualEntryUpdated')]
    public function updated(): void
    {
        $this->toast()->success('Cash wallet updated successfully')->send();
    }

    #[On('userManualEntryDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view(
            'livewire.show-manual-entries',
            [
                'wallets' => UserManualEntry::paginate(10),
            ],
        );
    }
}

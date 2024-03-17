<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Crypto\Models\UserKrakenAccount;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowKrakenAccounts extends Component
{
    public function delete(UserKrakenAccount $krakenAccount): void
    {
        $krakenAccount->delete();

        $this->dispatch('userKrakenAccountDeleted');
    }

    #[On('userKrakenAccountAdded')]
    #[On('userKrakenAccountUpdated')]
    #[On('userKrakenAccountDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.show-kraken-accounts', [
            'krakenAccounts' => UserKrakenAccount::paginate(10),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\UserBankAccount;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ShowBankAccounts extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user === null) {
            $this->redirect(route('login'));
        }
    }

    public function delete(UserBankAccount $bankAccount): void
    {
        $bankAccount->delete();

        $this->dispatch('bankAccountDeleted');
    }

    #[On('bankAccountDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.show-bank-accounts', [
            'bankAccounts' => UserBankAccount::paginate(10),
        ]);
    }
}

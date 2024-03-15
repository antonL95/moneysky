<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\UserBankAccount;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class ShowBankAccounts extends Component
{
    use WithPagination;
    use Interactions;

    public int $bankBalanceTotal = 0;

    public string $userCurrency = 'USD';

    public function mount(): void
    {
        $userSetting = auth()->user()?->settings()?->whereKey('currency')->first();
        $this->userCurrency = $userSetting->value ?? 'USD';

        $sum = UserBankAccount::sum('balance_cents');
        if (is_numeric($sum)) {
            $this->bankBalanceTotal = (int) $sum;
        }
    }

    public function delete(UserBankAccount $bankAccount): void
    {
        $bankAccount->delete();

        $this->dispatch('bankAccountDeleted');
    }

    #[On('bankAccountDeleted')]
    public function render(): View
    {
        if (session()->has('bank-account-success')) {
            $this->toast()->success(
                session('bank-account-success')
            );

            session()->forget('bank-account-success');
        }

        if (session()->has('bank-account-error')) {
            $this->toast()->error(
                session('bank-account-error')
            );

            session()->forget('bank-account-error');
        }

        return view('livewire.show-bank-accounts', [
            'bankAccounts' => UserBankAccount::paginate(10),
        ]);
    }
}

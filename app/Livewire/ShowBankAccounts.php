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
    public ?int $quantity = 10;
    public bool $bankInstitutionModal = false;
    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

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

    public function with(): array
    {
        return [
            'headers' => [
                ['key' => 'id', 'label' => 'ID'],
                ['key' => 'name', 'label' => 'Name'],
                ['key' => 'balance_cents', 'label' => 'Balance'],
            ],
            'rows' => UserBankAccount::orderBy(...array_values($this->sortBy))->paginate($this->quantity),
        ];
    }

    #[On('bankAccountDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.user-bank-account.show-bank-accounts', $this->with());
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\UserBankAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ShowBankAccounts extends Component
{
    use WithPagination;

    public ?int $quantity = 10;

    public bool $bankInstitutionModal = false;

    /**
     * @var string[]
     */
    public array $sort = ['column' => 'id', 'direction' => 'desc'];

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

    /**
     * @return array<string, array<int, array<int|string|bool|string>>|LengthAwarePaginator<UserBankAccount>>
     */
    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'id', 'label' => 'ID'],
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'balance_cents', 'label' => 'Balance'],
            ],
            'rows' => UserBankAccount::with('institution')
                ->orderBy(...array_values($this->sort))
                ->paginate($this->quantity),
        ];
    }

    #[On('bankAccountDeleted')]
    #[On('currency-updated')]
    public function render(): View
    {
        return view('livewire.user-bank-account.show-bank-accounts', $this->with());
    }
}

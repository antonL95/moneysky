<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\BankInstitution;
use App\Bank\Services\BankAccounts;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ConnectBankAccount extends Component
{
    #[Rule('required')]
    public ?int $institution = null;

    protected BankAccounts $bankAccounts;

    public function boot(BankAccounts $bankAccounts): void
    {
        $this->bankAccounts = $bankAccounts;
    }

    public function connect(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        $institution = BankInstitution::find($this->institution);

        if ($institution === null || $user === null || $user->subscribed()) {
            return;
        }

        $redirectLink = $this->bankAccounts->connect($institution, $user);

        $this->redirect($redirectLink);
    }

    public function render(): View
    {
        return view('livewire.connect-bank-account');
    }
}

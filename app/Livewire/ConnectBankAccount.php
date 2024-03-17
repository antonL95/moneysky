<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\BankInstitution;
use App\Bank\Services\BankService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class ConnectBankAccount extends Component
{
    use Interactions;

    #[Rule('required')]
    public ?int $institution = null;

    protected BankService $bankService;

    public function boot(BankService $bankService): void
    {
        $this->bankService = $bankService;
    }

    public function connect(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        if ($this->institution === null || $user === null || !$user->subscribed()) {
            $this->toast()->error('You need to select a bank institution to connect.');

            back();

            return;
        }

        $institution = BankInstitution::find($this->institution);

        if ($institution === null) {
            $this->toast()->error('Invalid bank institution selected.');

            back();

            return;
        }

        $redirectLink = $this->bankService->connect($institution, $user);

        $this->redirect($redirectLink);
    }

    public function render(): View
    {
        return view(
            'livewire.connect-bank-account',
        );
    }
}

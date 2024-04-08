<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\BankInstitution;
use App\Bank\Models\UserBankSession;
use App\Bank\Services\BankService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class ConnectBankAccount extends Component
{
    use Interactions;

    #[Validate('required')]
    public ?int $institution = null;

    /**
     * @var Collection<int, array{id: int, name: string, image: string, countries: string}>.>
     */
    public Collection $institutionsSearchable;

    protected BankService $bankService;

    public function boot(BankService $bankService): void
    {
        $this->bankService = $bankService;
    }

    public function connect(): void
    {
        $user = auth()->user();

        if ($user === null || !$user->canAddAdditionalResource(UserBankSession::class)) {
            $this->toast()->error(
                'Cannot connect to bank',
                'You need to upgrade your subscription in order to connect bank account.'
            )->send();

            $this->dispatch('close-institution-modal');

            return;
        }

        if ($this->institution === null) {
            $this->toast()->error('Error', 'You need to select a bank institution to connect.')->send();

            back();

            return;
        }

        $institution = BankInstitution::find($this->institution);

        if ($institution === null) {
            $this->toast()->error('Error', 'Invalid bank institution selected.')->send();

            back();

            return;
        }

        $redirectLink = $this->bankService->connect($institution, $user);

        $this->redirect($redirectLink);
    }

    public function render(): View
    {
        return view(
            'livewire.user-bank-account.connect-bank-account',
        );
    }
}

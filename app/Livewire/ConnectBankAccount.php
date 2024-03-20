<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Bank\Models\BankInstitution;
use App\Bank\Services\BankService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class ConnectBankAccount extends Component
{
    use Toast;

    #[Validate('required')]
    public ?int $institution = null;
    public Collection $institutionsSearchable;

    protected BankService $bankService;


    public function mount(BankService $bankService): void
    {
        $this->search();
        $this->bankService = $bankService;
    }


    public function connect(): void
    {
        $user = auth()->user();

        if (!$user?->subscribed()) {
            $this->redirect(route('billing'));
        }

        if ($this->institution === null || $user === null || !$user->subscribed()) {
            $this->error('You need to select a bank institution to connect.');

            back();

            return;
        }

        $institution = BankInstitution::find($this->institution);

        if ($institution === null) {
            $this->error('Invalid bank institution selected.');

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


    public function search(string $value = ''): void
    {
        $selectedOption = BankInstitution::where('id', $this->institution)->get();

        $this->institutionsSearchable = BankInstitution::query()
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption)
        ->map(
            function (BankInstitution $institution) {
                return [
                    'id' => $institution->id,
                    'name' => $institution->name,
                    'image' => $institution->logo_url,
                    'countries' => Arr::join($institution->countries, ','),
                ];
            },
        );
    }
}

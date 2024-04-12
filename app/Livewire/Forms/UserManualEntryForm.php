<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\UserManualEntry;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserManualEntryForm extends Form
{
    #[Validate('required')]
    public ?string $name;

    #[Validate('required')]
    public ?float $amount;

    public ?string $description = null;

    #[Validate('required')]
    public ?string $currency;

    public function store(): void
    {
        $this->validate();

        UserManualEntry::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'amount_cents' => (int) ($this->amount * 100),
            'currency' => $this->currency,
            'description' => $this->description,
        ]);
    }

    public function update(UserManualEntry $manualEntry): void
    {
        $this->validate();

        $manualEntry->update([
            'name' => $this->name,
            'amount_cents' => (int) ($this->amount * 100),
            'currency' => $this->currency,
            'description' => $this->description,
        ]);
    }
}

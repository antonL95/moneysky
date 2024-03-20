<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Crypto\Jobs\ProcessKrakenAccounts;
use App\Crypto\Models\UserKrakenAccount;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserKrakenAccountForm extends Form
{
    #[Validate('required')]
    public ?string $api_key = null;

    #[Validate('required')]
    public ?string $private_key = null;

    public function store(): void
    {
        $this->validate();

        $krakenAccount = UserKrakenAccount::create([
            'user_id' => auth()->id(),
            'api_key' => $this->api_key,
            'private_key' => $this->private_key,
        ]);

        ProcessKrakenAccounts::dispatch($krakenAccount);

        $this->reset();
    }

    public function update(UserKrakenAccount $krakenAccount): void
    {
        $this->validate();

        $krakenAccount->update([
            'api_key' => $this->api_key,
            'private_key' => $this->private_key,
        ]);

        ProcessKrakenAccounts::dispatch($krakenAccount);
    }
}

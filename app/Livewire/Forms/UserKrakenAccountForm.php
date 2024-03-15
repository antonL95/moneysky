<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Crypto\Jobs\ProcessKrakenAccounts;
use App\Crypto\Models\UserKrakenAccount;
use Livewire\Attributes\Rule;
use Livewire\Form;

class UserKrakenAccountForm extends Form
{
    #[Rule('required')]
    public ?string $apiKey = null;

    #[Rule('required')]
    public ?string $privateKey = null;

    public function store(): void
    {
        $this->validate();

        $krakenAccount = UserKrakenAccount::create([
            'user_id' => auth()->id(),
            'api_key' => $this->apiKey,
            'private_key' => $this->privateKey,
        ]);

        ProcessKrakenAccounts::dispatch($krakenAccount);

        $this->reset();
    }

    public function update(UserKrakenAccount $krakenAccount): void
    {
        $this->validate();

        $krakenAccount->update([
            'api_key' => $this->apiKey,
            'private_key' => $this->privateKey,
        ]);

        ProcessKrakenAccounts::dispatch($krakenAccount);
    }
}

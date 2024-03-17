<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\MarketData\Jobs\ProcessStockMarket;
use App\MarketData\Models\UserStockMarket;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserStockMarketForm extends Form
{
    #[Validate(['required', 'string'])]
    public ?string $ticker;

    #[Validate(['required'])]
    public ?float $amount;

    public function store(): void
    {
        $this->validate();

        $stockMarket = UserStockMarket::create([
            'user_id' => auth()->id(),
            'ticker' => $this->ticker,
            'amount' => $this->amount,
        ]);

        ProcessStockMarket::dispatch($stockMarket);
    }

    public function update(UserStockMarket $stockMarket): void
    {
        $this->validate();

        $stockMarket->update([
            'ticker' => $this->ticker,
            'amount' => $this->amount,
        ]);

        ProcessStockMarket::dispatch($stockMarket);
    }
}

<div>

    <div class="flex justify-center mb-10 md:grid md:grid-cols-2 max-h-[400px]">
        <div>
            <x-mary-chart wire:model="netWorthChart" class="h-[400px]" />
        </div>
        <div>
        {{--reapeateble expeneses chart for the month--}}
        </div>
    </div>

    <x-mary-table :headers="$headers" :rows="$rows" with-pagination class="bg-base-100" :sort-by="$sortBy" >
        @scope('cell_tag', $row)
            <x-mary-badge
                :value="$row->userTransactionTag?->tag ?? $row->transactionTag?->tag ?? __('unknown')"
                class="text-white border-none text-sm h-fit text-center"
                style="background-color: {{$row->userTransactionTag?->color ?? $row->transactionTag?->color ?? '#ccc'}}" />
        @endscope
        @scope('cell_balance_cents', $row)
            <x-amount-format :amount="$row->balance_cents" :amount-currency="$row->currency" />
        @endscope
        @scope('cell_bank_account', $row)
            {{$row->userBankAccount->name}}
        @endscope
        @scope('cell_booked_at', $row)
            {{$row->booked_at->diffForHumans()}}
        @endscope
    </x-mary-table>
</div>

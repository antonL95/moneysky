<div>
    <div class="flex justify-end">
        <x-mary-button class="btn btn-primary" link="{{route('app.add-crypto-wallets')}}" wire:navigate>
            <x-mary-icon name="fas.plus" class="w-[20px] h-[20px] pr-2"/>
            {{ __('Add crypto wallet') }}
        </x-mary-button>
    </div>

    <x-mary-table :headers="$headers" :rows="$rows" with-pagination x-mary-checkbox:sort-by="$sortBy" >
        @scope('cell_balance_cents', $row)
        <x-amount-format :amount="$row->balance_cents" :amount-currency="'USD'" />
        @endscope
        {{-- Special `actions` slot --}}
        @scope('actions', $row)
        <div class="flex items-center justify-end">
            <x-mary-button icon="fas.pencil" href="{{route('app.edit-crypto-wallets', ['wallet' => $row->id])}}" wire:navigate class="btn-sm bg-info" />
            <x-mary-button icon="o-trash" wire:click="delete({{ $row->id }})" class="btn-sm bg-warning" />
        </div>
        @endscope
    </x-mary-table>
</div>

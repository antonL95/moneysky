<div>
    <div class="flex justify-end">
        <x-mary-button class="btn btn-primary" link="{{route('app.add-stock-market')}}" wire:navigate>
            <x-mary-icon name="fas.plus" class="w-[20px] h-[20px] pr-2"/>
            {{ __('Add stock ticker') }}
        </x-mary-button>
    </div>

    <x-mary-table :headers="$headers" :rows="$rows" with-pagination class="" :sort-by="$sortBy" >
        @scope('cell_price_cents', $row)
        <x-amount-format :amount="$row->price_cents" :amount-currency="'USD'" />
        @endscope
        {{-- Special `actions` slot --}}
        @scope('actions', $row)
        <div class="flex items-center justify-end">
            <x-mary-button icon="fas.pencil" href="{{route('app.update-stock-market', ['ticker' => $row->id])}}" wire:navigate class="btn-sm bg-yellow-300 text-white" />
            <x-mary-button icon="o-trash" wire:click="delete({{ $row->id }})" spinner class="btn-sm bg-red-500 text-white" />
        </div>
        @endscope
    </x-mary-table>
</div>

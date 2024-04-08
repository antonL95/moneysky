<div>
    <div class="flex justify-end">
        @teleport('body')
            <x-ts-modal id="add-stock-ticker" x-on:close="$modalClose(add-stock-ticker)">
                <livewire:add-user-stock-market/>
            </x-ts-modal>
        @endteleport
        <x-table-header-button modal="add-stock-ticker" :title="__('Add ticker')"/>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination loading :$sort >
        @interact('column_price_cents', $row)
            <x-amount-format :amount="$row->price_cents" :amount-currency="'USD'" />
        @endinteract
        @interact('column_action', $row)
            <x-ts-button.circle color="yellow"
                                icon="pencil"
                                x-on:click="$modalOpen('edit-stock-ticker-{{ $row->id }}')"/>

            @teleport('body')
            <x-ts-modal id="edit-stock-ticker-{{ $row->id }}"
                        x-on:close="$modalClose('edit-stock-ticker-{{ $row->id }}')">
                <livewire:update-user-stock-market :ticker="$row" :key="uniqid()"/>
            </x-ts-modal>
            @endteleport
            <x-ts-button.circle color="red"
                                icon="trash"
                                wire:click="delete('{{ $row->id }}')"/>
        @endinteract
    </x-ts-table>
</div>

<div>
    <div class="flex justify-end">
        @teleport('body')
        <x-ts-modal id="add-crypto-wallet" x-on:close="$modalClose('add-crypto-wallet')">
            <livewire:add-user-crypto-wallet/>
        </x-ts-modal>
        @endteleport
        <x-table-header-button modal="add-crypto-wallet" :title="__('Add crypto wallet')"/>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination loading :$sort>
        @interact('column_balance_cents', $row)
            <x-amount-format :amount="$row->balance_cents" :amount-currency="'USD'"/>
        @endinteract
        @interact('column_action', $row)
            <x-ts-button.circle color="yellow"
                                icon="pencil"
                                x-on:click="$modalOpen('edit-crypto-wallet-{{ $row->id }}')"/>

            @teleport('body')
            <x-ts-modal id="edit-crypto-wallet-{{ $row->id }}"
                        x-on:close="$modalClose('edit-crypto-wallet-{{ $row->id }}')">
                <livewire:update-user-crypto-wallet :wallet="$row" :key="uniqid()"/>
            </x-ts-modal>
            @endteleport
            <x-ts-button.circle color="red"
                                icon="trash"
                                wire:click="delete('{{ $row->id }}')"/>
        @endinteract
    </x-ts-table>
</div>

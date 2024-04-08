<div>
    <div class="flex justify-end">
        @teleport('body')
            <x-ts-modal id="add-kraken-account">
                <livewire:add-user-kraken-account/>
            </x-ts-modal>
        @endteleport
        <x-table-header-button :title="__('Add account')" modal="add-kraken-account"/>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination loading :$sort >
        @interact('column_balance_cents', $row)
            <x-amount-format :amount="$row->balance_cents" :amount-currency="'USD'" />
        @endinteract
        @interact('column_action', $row)
            <x-ts-button.circle color="yellow"
                                icon="pencil"
                                x-on:click="$modalOpen('edit-kraken-account-{{ $row->id }}')"/>

            @teleport('body')
            <x-ts-modal id="edit-kraken-account-{{ $row->id }}"
                        x-on:close="$modalClose('edit-kraken-account-{{ $row->id }}')">
                <livewire:update-user-kraken-account :account="$row" :key="uniqid()"/>
            </x-ts-modal>
            @endteleport
            <x-ts-button.circle color="red"
                                icon="trash"
                                wire:click="delete('{{ $row->id }}')"/>
        @endinteract
    </x-ts-table>
</div>

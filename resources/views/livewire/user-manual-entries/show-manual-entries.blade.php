<div>
    <div class="flex justify-end">
        @teleport('body')
            <x-ts-modal id="add-manual-entry">
                <livewire:add-user-manual-entries/>
            </x-ts-modal>
        @endteleport
        <x-table-header-button modal="add-manual-entry" :title="__('Add cash wallet')"/>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination loading :$sort >
        @interact('column_amount_cents', $row)
            <x-amount-format :amount="$row->amount_cents" :amount-currency="$row->currency" />
        @endinteract
        @interact('column_action', $row)
            <x-ts-button.circle color="yellow"
                                icon="pencil"
                                x-on:click="$modalOpen('edit-manual-entry-{{ $row->id }}')"/>

            @teleport('body')
            <x-ts-modal id="edit-manual-entry-{{ $row->id }}"
                        x-on:close="$modalClose('edit-manual-entry-{{ $row->id }}')">
                <livewire:update-user-manual-entries :wallet="$row" :key="uniqid()"/>
            </x-ts-modal>
            @endteleport
            <x-ts-button.circle color="red"
                                icon="trash"
                                wire:click="delete('{{ $row->id }}')"/>
        @endinteract
    </x-ts-table>
</div>

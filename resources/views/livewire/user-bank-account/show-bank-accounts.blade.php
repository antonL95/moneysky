<div>
    <div class="flex justify-end">
        @teleport('body')
            <x-ts-modal id="bank-institution-modal" x-on:close-institution-modal="$modalClose('bank-institution-modal')">
                <livewire:connect-bank-account/>
            </x-ts-modal>
        @endteleport
        <x-table-header-button modal="bank-institution-modal" :title="__('Connect bank')"/>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination>
        @interact('column_balance_cents', $row)
        <x-amount-format :amount="$row->balance_cents" :amount-currency="$row->currency"/>
        @endinteract
        @interact('column_name', $row)
        {{ $row->name ?? $row->institution->name . ' ('.$row->currency.')' }}
        @endinteract
    </x-ts-table>
</div>

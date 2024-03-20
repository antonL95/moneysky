<div>
    <div class="flex justify-end">
        @teleport('body')
        <x-mary-modal wire:model="bankInstitutionModal" class="backdrop-blur">
            <livewire:connect-bank-account/>
        </x-mary-modal>
        @endteleport
        <x-mary-button @click="$wire.bankInstitutionModal = true">
            <x-mary-icon name="fas.plus" class="w-[20px] h-[20px] pr-2"/>
            {{ __('Connect bank') }}
        </x-mary-button>
    </div>

    <x-mary-table :headers="$headers" :rows="$rows" with-pagination class="bg-base-100" :sort-by="$sortBy" >
        @scope('cell_balance_cents', $row)
        <x-amount-format :amount="$row->balance_cents" :amount-currency="$row->currency" />
        @endscope
    </x-mary-table>
</div>

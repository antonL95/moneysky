@php use App\Models\UserSetting @endphp
<div>
    <div class="flex justify-center mb-10 md:grid md:grid-cols-2 max-h-[400px]">
        <div class="flex flex-col md:flex-row">
            @if($bankAccountsSum !== null)
                <x-mary-stat
                    title="{{__('Bank accounts')}}"
                    icon="c-building-library"
                    tooltip="{{__('Total sum of your bank account balances')}}">
                    <x-slot:value>
                        <x-amount-format :amount="$bankAccountsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-mary-stat>
            @endif
            @if($cryptoSum !== null)
                <x-mary-stat
                    title="{{__('Crypto wallets')}}"
                    icon="fab.bitcoin"
                    tooltip="{{__('Total sum of your crypto wallets')}}">
                    <x-slot:value>
                        <x-amount-format :amount="$cryptoSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-mary-stat>
            @endif
            @if($stocksSum !== null)
                <x-mary-stat
                    title="{{__('Stock market')}}"
                    icon="fas.rocket"
                    tooltip="{{__('Total sum of your stocks')}}">
                    <x-slot:value>
                        <x-amount-format :amount="$stocksSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-mary-stat>
            @endif
            @if($cashWalletsSum !== null)
                <x-mary-stat
                    title="{{__('Cash wallets')}}"
                    icon="fas.wallet"
                    tooltip="{{__('Total sum of your cash wallets')}}">
                    <x-slot:value>
                        <x-amount-format :amount="$cashWalletsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-mary-stat>
            @endif
        </div>
        <div>
        </div>
    </div>

    <x-mary-table :headers="$headers" :rows="$rows" with-pagination x-mary-checkbox:sort-by="$sortBy">
        @scope('cell_tag', $row)
        <x-mary-badge
            :value="$row->userTransactionTag?->tag ?? $row->transactionTag?->tag ?? __('unknown')"
            class=" border-none text-sm h-fit text-center"
            style="background-color: {{$row->userTransactionTag?->color ?? $row->transactionTag?->color ?? '#ccc'}}"/>
        @endscope
        @scope('cell_balance_cents', $row)
        <x-amount-format :amount="$row->balance_cents" :amount-currency="$row->currency"/>
        @endscope
        @scope('cell_bank_account', $row)
        {{$row->userBankAccount->name}}
        @endscope
        @scope('cell_booked_at', $row)
        {{$row->booked_at->diffForHumans()}}
        @endscope
    </x-mary-table>
</div>

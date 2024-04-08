@php use App\UserSetting\Models\UserSetting@endphp
<div>
    <div class="flex justify-center mb-10 md:grid md:grid-cols-2 max-h-[400px]">
        <div class="flex flex-col md:flex-row flex-wrap justify-around gap-1">
            @if($bankAccountsSum !== null)
                <x-ts-stats
                    class="w-5/12"
                    title="{{__('Bank accounts')}}"
                    icon="credit-card">
                    <x-slot:number>
                        <x-amount-format :amount="$bankAccountsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:number>
                </x-ts-stats>
            @endif
            @if($cryptoSum !== null)
                <x-ts-stats
                    class="w-5/12"
                    title="{{__('Crypto wallets')}}"
                    icon="currency-eth">
                    <x-slot:number>
                        <x-amount-format :amount="$cryptoSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:number>
                </x-ts-stats>
            @endif
            @if($stocksSum !== null)
                <x-ts-stats
                    class="w-5/12"
                    title="{{__('Stock market')}}"
                    icon="chart-bar">
                    <x-slot:number>
                        <x-amount-format :amount="$stocksSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:number>
                </x-ts-stats>
            @endif
            @if($cashWalletsSum !== null)
                <x-ts-stats
                    class="w-5/12"
                    title="{{__('Cash wallets')}}"
                    icon="wallet">
                    <x-slot:number>
                        <x-amount-format :amount="$cashWalletsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:number>
                </x-ts-stats>
            @endif
        </div>
        <div class="flex flex-col md:flex-row">

        </div>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate simple-pagination loading :$sort >
        @interact('column_tag', $row)
            <x-ts-badge
                :text="$row->userTransactionTag?->tag ?? $row->transactionTag?->tag ?? __('unknown')"
                class="border-none text-sm h-fit text-center text-black"
                style="background-color: {{$row->userTransactionTag?->color ?? $row->transactionTag?->color ?? '#ccc'}}"/>
        @endinteract
        @interact('column_balance_cents', $row)
            <x-amount-format :amount="$row->balance_cents" :amount-currency="$row->currency"/>
        @endinteract
        @interact('column_bank_account', $row)
            {{$row->userBankAccount->name}}
        @endinteract
        @interact('column_booked_at', $row)
            {{$row->booked_at->diffForHumans()}}
        @endinteract
    </x-ts-table>
</div>

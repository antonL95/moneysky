@php use App\UserSetting\Models\UserSetting@endphp
<div>
    <div class="flex justify-center mb-10 md:grid md:grid-cols-2">
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">
            @if($bankAccountsSum !== null)
                <x-stats-card
                    title="{{__('Bank accounts')}}"
                    icon="credit-card">
                    <x-slot:value>
                        <x-amount-format :amount="$bankAccountsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-stats-card>
            @endif
            @if($cryptoSum !== null)
                <x-stats-card
                    title="{{__('Crypto wallets')}}"
                    icon="currency-eth">
                    <x-slot:value>
                        <x-amount-format :amount="$cryptoSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-stats-card>
            @endif
            @if($stocksSum !== null)
                <x-stats-card
                    title="{{__('Stock market')}}"
                    icon="chart-bar">
                    <x-slot:value>
                        <x-amount-format :amount="$stocksSum" :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-stats-card>
            @endif
            @if($cashWalletsSum !== null)
                <x-stats-card
                    title="{{__('Cash wallets')}}"
                    icon="wallet">
                    <x-slot:value>
                        <x-amount-format :amount="$cashWalletsSum"
                                         :amount-currency="UserSetting::getCurrencyWithDefault()"/>
                    </x-slot:value>
                </x-stats-card>
            @endif
        </dl>
        <div class="flex flex-col md:flex-row">

        </div>
    </div>

    <x-ts-table :headers="$headers" :rows="$rows" paginate loading :$sort >
        @interact('column_tag', $row)
            <x-badge
                :text="$row->userTransactionTag?->tag ?? $row->transactionTag?->tag ?? __('unknown')"
                :color="$row->userTransactionTag?->color ?? $row->transactionTag?->color ?? '#ccc'"/>
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

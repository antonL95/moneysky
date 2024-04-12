<nav class="flex flex-1 flex-col w-full">
    <ul role="list" class="flex flex-1 flex-col gap-y-2">
        <x-navigation.navigation-item :route="route('app.home')" :title="__('Dashboard')">
            <x-slot:icon>
                <x-ts-icon name="chart-pie-slice" class="h-6 w-6"/>
            </x-slot:icon>
        </x-navigation.navigation-item>
        <x-navigation.navigation-item :route="route('app.bank-accounts')" :title="__('Bank accounts')">
            <x-slot:icon>
                <x-ts-icon name="credit-card" class="h-6 w-6"/>
            </x-slot:icon>
        </x-navigation.navigation-item>
        <x-navigation.navigation-item :route="route('app.crypto-wallets')" :title="__('Crypto wallets')">
            <x-slot:icon>
                <x-ts-icon name="currency-eth" class="h-6 w-6"/>
            </x-slot:icon>
        </x-navigation.navigation-item>
        <x-navigation.navigation-item :route="route('app.kraken-accounts')" :title="__('Kraken accounts')">
            <x-slot:icon>
                <x-kraken-logo class="h-6 w-6 stroke-black fill-black" />
            </x-slot:icon>
        </x-navigation.navigation-item>
        <x-navigation.navigation-item :route="route('app.stock-market')" :title="__('Stock tickers')">
            <x-slot:icon>
                <x-ts-icon name="chart-bar" class="h-6 w-6"/>
            </x-slot:icon>
        </x-navigation.navigation-item>
        <x-navigation.navigation-item :route="route('app.manual-entries')" :title="__('Cash wallets')">
            <x-slot:icon>
                <x-ts-icon name="wallet" class="h-6 w-6"/>
            </x-slot:icon>
        </x-navigation.navigation-item>
        @if(!auth()->user()->checkSubscriptionType('plus') && !auth()->user()->checkSubscriptionType('unlimited'))
            <li class="-mx-6 mt-auto">
                <a href="{{ route('subscription-checkout', ['plan' => 'unlimited']) }}" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50">
                    <span aria-hidden="true">{{ __('Subscribe to unlimited') }}</span>
                </a>
            </li>
            <li class="-mx-6">
                <a href="{{ route('subscription-checkout', ['plan' => 'plus']) }}" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50">
                    <span aria-hidden="true">{{ __('Subscribe to plus') }}</span>
                </a>
            </li>
        @elseif(auth()->user()->checkSubscriptionType('plus') && !auth()->user()->checkSubscriptionType('unlimited'))
            <li class="-mx-6 mt-auto">
                <a href="{{ route('billing') }}" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50">
                    <span aria-hidden="true">{{ __('Upgrade to unlimited') }}</span>
                </a>
            </li>
        @endif
    </ul>
</nav>

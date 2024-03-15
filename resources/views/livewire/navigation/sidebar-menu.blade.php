<aside
    class="fixed top-0 left-0 z-10 w-64 h-screen pt-14 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidenav"
    id="drawer-navigation">
    <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
        <ul class="space-y-2">
            <li>
                <a href="{{route('app.home')}}"
                   wire:navigate
                   class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <x-fas-chart-pie class="w-[20px] h-[20px]"/>
                    <span class="ml-3">
                        {{__('Overview')}}
                    </span>
                </a>
            </li>
            <li>
                <a href="{{route('app.crypto-wallets')}}"
                        wire:navigate
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <x-fab-bitcoin class="w-[20px] h-[20px]"/>
                    <span class="ml-3">
                        {{__('Crypto wallets')}}
                        <x-subscription-only-badge/>
                    </span>
                </a>
            </li>
            <li>
                <button href="{{route('app.bank-accounts')}}"
                        wire:navigate
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <x-fas-building-columns class="w-[20px] h-[20px]"/>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">
                        {{__('Bank accounts')}}
                        <x-subscription-only-badge/>
                    </span>
                </button>
            </li>
            <li>
                <button href="{{route('app.kraken-accounts')}}"
                        wire:navigate
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                    <x-kraken-logo/>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">
                        {{__('Kraken accounts')}}
                    </span>
                </button>
            </li>
        </ul>
    </div>
</aside>

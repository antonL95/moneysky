<x-layouts.guest>
    <x-slot name="header">
        <section>
            <div class="relative isolate px-6 pt-14 lg:px-8">
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                     aria-hidden="true">
                    <div
                        class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                </div>
                <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                            {{__('Automate your budgets with ease.')}}
                        </h1>
                        <p class="mt-6 text-lg leading-8">
                            {{__('Automate your finance tracking and stay informed with real-time updates.')}}
                        </p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <x-mary-button class="btn btn-primary" href="{{route('register')}}" wire:navigate>
                                {{__('Get started')}}
                            </x-mary-button>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                    aria-hidden="true">
                    <div
                        class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                </div>
            </div>
        </section>
    </x-slot>

    <section>
        <div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <p class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                        {{__('Get rid of the budget spreadsheets')}}
                    </p>
                    <p class="mt-6 text-lg leading-8">
                        {{__('Elevate your finance management with us, your all-in-one app for tracking savings, investments, and spending. Enjoy real-time updates and insights to stay informed.')}}
                    </p>

                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-mary-icon name="fas.building-columns"
                                                 class="h-6 w-6 text-base-100 dark:text-inherit"/>
                                </div>
                                {{__('Automated Bank Account Sync')}}
                            </dt>
                            <dd class="mt-2 leading-7">
                                {{__('Easily check your transactions and balances from connected bank accounts with real-time updates and notifications.')}}
                            </dd>
                        </div>

                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-mary-icon name="fab.bitcoin" class="h-6 w-6 text-base-100 dark:text-inherit"/>
                                </div>
                                {{__('Crypto Wallet Balances')}}
                            </dt>
                            <dd class="mt-2 leading-7">
                                {{__('Monitor your cryptocurrency holdings with our app, ensuring you\'re up-to-date on the value of your digital assets.')}}
                            </dd>
                        </div>

                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-mary-icon name="fas.bitcoin-sign"
                                                 class="h-6 w-6 text-base-100 dark:text-inherit"/>
                                </div>
                                {{__('Kraken Exchange Balances')}}
                            </dt>
                            <dd class="mt-2 leading-7">
                                {{__('Stay informed about your balance Kraken exchange.')}}
                            </dd>
                        </div>

                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-mary-icon name="fas.rocket" class="h-6 w-6 text-base-100 dark:text-inherit"/>
                                </div>
                                {{__('Stock Market Tickers')}}
                            </dt>
                            <dd class="mt-2 leading-7">
                                {{__('Get instant access to stock market tickers, and track your investment portfolio.')}}
                            </dd>
                        </div>

                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-mary-icon name="fas.wallet" class="h-6 w-6 text-base-100 dark:text-inherit"/>
                                </div>
                                {{__('Manual Cash Accounts')}}
                            </dt>
                            <dd class="mt-2 leading-7">
                                {{__('Effortlessly manage your cash wallets. We let you keep track of cash flows and other manual entries, ensuring a comprehensive overview.')}}
                            </dd>
                        </div>
                    </dl>
                    <div class="flex justify-center mt-4">
                        <div class="px-2 py-2 text-sm rounded-full ">
                            <span class="text-sm font-medium px-2">
                                {{__('🚀 More features coming soon')}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl sm:text-center">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        {{__('Simple no-tricks pricing')}}
                    </h2>
                </div>
                <div
                    class="mx-auto mt-16 max-w-2xl rounded-3xl ring-1 ring-gray-200 sm:mt-20 lg:mx-0 lg:flex lg:max-w-none">
                    <div class="p-8 sm:p-10 lg:flex-auto">
                        <h3 class="text-2xl font-bold tracking-tight">{{__('All access')}}</h3>
                        <p class="mt-6 leading-7">
                            {{__('All features are included with no limits. No hidden fees. Cancel anytime.')}}
                        </p>
                        <div class="mt-10 flex items-center gap-x-4">
                            <h4 class="flex-none text-sm font-semibold leading-6">{{__('What\'s included')}}</h4>
                            <div class="h-px flex-auto bg-gray-100"></div>
                        </div>
                        <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 sm:grid-cols-2 sm:gap-6">
                            <li class="flex gap-x-3">
                                <x-mary-icon name="fas.check" class="h-6 w-5 flex-none"/>
                                {{__('Unlimited bank accounts')}}
                            </li>
                            <li class="flex gap-x-3">
                                <x-mary-icon name="fas.check" class="h-6 w-5 flex-none"/>
                                {{__('Unlimited crypto wallets')}}
                            </li>
                            <li class="flex gap-x-3">
                                <x-mary-icon name="fas.check" class="h-6 w-5 flex-none"/>
                                {{__('Unlimited Kraken exchange accounts')}}
                            </li>
                            <li class="flex gap-x-3">
                                <x-mary-icon name="fas.check" class="h-6 w-5 flex-none"/>
                                {{__('Unlimited stock market tickers')}}
                            </li>
                            <li class="flex gap-x-3">
                                <x-mary-icon name="fas.check" class="h-6 w-5 flex-none"/>
                                {{__('Unlimited manual cash accounts')}}
                            </li>
                        </ul>
                    </div>
                    <div class="-mt-2 p-2 lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
                        <livewire:pricing-table/>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <livewire:newsletter-subscribe/>
</x-layouts.guest>

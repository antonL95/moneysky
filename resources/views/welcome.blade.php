<x-guest-layout>
    <div class="bg-white">
        <main class="isolate">
            <!-- Hero section -->
            <div class="relative pt-14">
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                     aria-hidden="true">
                    <div
                        class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                </div>
                <div class="py-24 sm:py-32">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                                {{ __('Automate your budgets with ease.') }}
                            </h1>
                            <p class="mt-6 text-lg leading-8 text-gray-600">
                                {{ __('Automate your finance tracking and stay informed with real-time updates.') }}
                            </p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                                <x-link-button href="{{ route('register') }}"
                                               wire:navigate title="{{ __('Get started') }}"/>
                            </div>
                        </div>
                        {{--<div class="mt-16 flow-root sm:mt-24">
                            <div
                                class="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-inset ring-gray-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                                <img src="https://tailwindui.com/img/component-images/project-app-screenshot.png"
                                     alt="App screenshot" width="2432" height="1442"
                                     class="rounded-md shadow-2xl ring-1 ring-gray-900/10">
                            </div>
                        </div>--}}
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

            <!-- Feature section -->
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600">
                        {{ __('Features') }}
                    </h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        {{ __('Get rid of the budget spreadsheets') }}
                    </p>
                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        {{ __('Elevate your finance management with us, your all-in-one app for tracking savings, investments, and spending. Enjoy real-time updates and insights to stay informed.') }}
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div class="relative pl-16">
                            <dt class="font-semibold leading-7  ">
                                <div
                                    class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <x-ts-icon name="credit-card" class="h-6 w-6 text-white"/>
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
                                    <x-ts-icon name="currency-btc" class="h-6 w-6 text-white"/>
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
                                    <x-kraken-logo class="h-6 w-6 stroke-white fill-white"/>
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
                                    <x-ts-icon name="chart-bar" class="h-6 w-6 text-white"/>
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
                                    <x-ts-icon name="wallet" class="h-6 w-6 text-white"/>
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

            <livewire:price-table />

            <!-- CTA section -->
            <div class="relative -z-10 mt-32 px-6 lg:px-8">
                <div
                    class="absolute inset-x-0 top-1/2 -z-10 flex -translate-y-1/2 transform-gpu justify-center overflow-hidden blur-3xl sm:bottom-0 sm:right-[calc(50%-6rem)] sm:top-auto sm:translate-y-0 sm:transform-gpu sm:justify-end"
                    aria-hidden="true">
                    <div
                        class="aspect-[1108/632] w-[69.25rem] flex-none bg-gradient-to-r from-[#ff80b5] to-[#9089fc] opacity-25"
                        style="clip-path: polygon(73.6% 48.6%, 91.7% 88.5%, 100% 53.9%, 97.4% 18.1%, 92.5% 15.4%, 75.7% 36.3%, 55.3% 52.8%, 46.5% 50.9%, 45% 37.4%, 50.3% 13.1%, 21.3% 36.2%, 0.1% 0.1%, 5.4% 49.1%, 21.4% 36.4%, 58.9% 100%, 73.6% 48.6%)"></div>
                </div>
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        {{ __('Elevate your budgets.') }}
                        <br>Start
                        using our app today.</h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-600">
                        {{ __('Streamline your financial world in one intuitive platform, where tracking your budget and net worth becomes effortlessly insightful. MoneySky.app transforms complexity into simplicity, empowering you to make smarter financial decisions with confidence.') }}
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <x-link-button wire:navigate href="{{ route('register') }}" title="{{ __('Get started') }}"/>
                    </div>
                </div>
            </div>
            <livewire:newsletter-subscribe/>
        </main>
    </div>
</x-guest-layout>

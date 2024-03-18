<div>
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
                        <h1 class="text-4xl font-bold tracking-tight text-dark-900 dark:text-primary-50 sm:text-6xl">
                            Get a hold of your finances
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-dark-900 dark:text-primary-50">
                            Track your spending, save money, and get personalized insights you can use to improve your
                            financial health.
                        </p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <x-button-link href="">
                                Get started
                            </x-button-link>
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

    <!-- Feature list -->
    <section>
        <div class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600">Master Your Money</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-dark-900 dark:text-primary-50 sm:text-4xl">Take Control of Your Financial Future</p>
                    <p class="mt-6 text-lg leading-8 text-dark-900 dark:text-primary-50">
                        Elevate your finance management with us, your all-in-one app for tracking savings, investments, and spending. Enjoy real-time updates and insights to stay informed. Join a community that's already optimizing their financial journey.
                    </p>

                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-dark-900 dark:text-primary-50">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <!-- Icon placeholder -->
                                    <x-fas-building-columns class="h-6 w-6 text-primary-50"/>
                                </div>
                                Transaction and Balance Checking
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-dark-800 dark:text-primary-100">Easily check your transactions and balances from connected bank accounts with real-time updates and notifications.</dd>
                        </div>

                        <!-- Feature 2: Crypto Wallet Balances -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-dark-900 dark:text-primary-50">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <!-- Icon placeholder -->
                                    <x-fab-bitcoin class="h-6 w-6 text-primary-50"/>
                                </div>
                                Crypto Wallet Balances
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-dark-800 dark:text-primary-100">Monitor your cryptocurrency holdings with our app, ensuring you're up-to-date on the value of your digital assets.</dd>
                        </div>

                        <!-- Feature 3: Kraken Exchange Balances -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-dark-900 dark:text-primary-50">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-600">
                                    <!-- Icon placeholder -->
                                    <x-kraken-logo/>
                                </div>
                                Kraken Exchange Balances
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-dark-800 dark:text-primary-100">Stay informed about your Kraken exchange balances.</dd>
                        </div>

                        <!-- Feature 4: Stock Market Tickers -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-dark-900 dark:text-primary-50">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <!-- Icon placeholder -->
                                    <x-fas-rocket class="h-6 w-6 text-primary-50"/>
                                </div>
                                Stock Market Tickers
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-dark-800 dark:text-primary-100">Get instant access to stock market tickers, and track your investment portfolio performance anytime, anywhere.</dd>
                        </div>

                        <!-- Feature 5: Manual Cash Accounts -->
                        <div class="relative pl-16">
                            <dt class="text-base font-semibold leading-7 text-dark-900 dark:text-primary-50">
                                <div class="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <!-- Icon placeholder -->
                                    <x-fas-wallet class="h-6 w-6 text-primary-50"/>
                                </div>
                                Manual Cash Accounts
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-dark-800 dark:text-primary-100">Effortlessly manage your cash wallets and manual transactions. We let you keep track of cash flows and other manual entries, ensuring a comprehensive overview.</dd>
                        </div>
                    </dl>
                    <div class="flex justify-center mt-4">
                        <div class="px-2 py-2 text-sm text-primary-50 bg-dark-900 rounded-full dark:bg-primary-950 dark:text-primary-50">
                            <span class="text-sm font-medium px-2">
                                More features coming soon
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
                    <h2 class="text-3xl font-bold tracking-tight text-dark-900 dark:text-primary-50 sm:text-4xl">Simple no-tricks pricing</h2>
                </div>
                <div class="mx-auto mt-16 max-w-2xl rounded-3xl ring-1 ring-gray-200 sm:mt-20 lg:mx-0 lg:flex lg:max-w-none">
                    <div class="p-8 sm:p-10 lg:flex-auto">
                        <h3 class="text-2xl font-bold tracking-tight text-dark-900 dark:text-primary-50">Subscription</h3>
                        <p class="mt-6 text-base leading-7 text-dark-900 dark:text-primary-50">
                            All features are included with no limits. No hidden fees. Cancel anytime.
                        </p>
                        <div class="mt-10 flex items-center gap-x-4">
                            <h4 class="flex-none text-sm font-semibold leading-6 text-indigo-600">What’s included</h4>
                            <div class="h-px flex-auto bg-gray-100"></div>
                        </div>
                        <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 text-dark-900 dark:text-primary-50 sm:grid-cols-2 sm:gap-6">
                            <li class="flex gap-x-3">
                                <x-fas-check class="h-6 w-5 flex-none text-indigo-600"/>
                                Unlimited bank accounts
                            </li>
                            <li class="flex gap-x-3">
                                <x-fas-check class="h-6 w-5 flex-none text-indigo-600"/>
                                Unlimited crypto wallets
                            </li>
                            <li class="flex gap-x-3">
                                <x-fas-check class="h-6 w-5 flex-none text-indigo-600"/>
                                Unlimited Kraken exchange accounts
                            </li>
                            <li class="flex gap-x-3">
                                <x-fas-check class="h-6 w-5 flex-none text-indigo-600"/>
                                Unlimited stock market tickers
                            </li>
                            <li class="flex gap-x-3">
                                <x-fas-check class="h-6 w-5 flex-none text-indigo-600"/>
                                Unlimited manual cash accounts
                            </li>
                        </ul>
                    </div>
                    <div class="-mt-2 p-2 lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
                        <div class="rounded-2xl py-10 text-center ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-16">
                            <div class="mx-auto max-w-xs px-8">
                                <p class="text-base font-semibold text-dark-900 dark:text-primary-50">Monthly</p>
                                <p class="mt-6 flex items-baseline justify-center gap-x-2">
                                    <span class="text-5xl font-bold tracking-tight text-dark-900 dark:text-primary-50">$9,99</span>
                                    <span class="text-sm font-semibold leading-6 tracking-wide text-dark-900 dark:text-primary-50">USD</span>
                                </p>
                                <x-button-link href="{{route('register')}}" class="mt-10 block w-full text-center justify-center">Get access</x-button-link>
                                <p class="mt-6 text-xs leading-5 text-dark-900 dark:text-primary-50">Invoices and receipts available for easy company reimbursement</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <livewire:newsletter-subscribe key="{{Str::random(32)}}"/>
</div>

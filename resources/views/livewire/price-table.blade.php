<div class="py-24" id="pricing">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base font-semibold leading-7 text-indigo-600">
                {{ __('Pricing') }}
            </h2>
            <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                {{ __('Simple no-tricks pricing') }}
            </p>
        </div>
        <div
            class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-y-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3"
        >
            <div
                class="flex flex-col justify-between rounded-3xl bg-white p-8 ring-1 ring-gray-200 lg:mt-8 lg:rounded-r-none xl:p-10"
            >
                <div>
                    <div class="flex items-center justify-between gap-x-4">
                        <h3 id="tier-freelancer" class="text-lg font-semibold leading-8 text-gray-900">
                            {{ __('Free') }}
                        </h3>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">
                        {{ __('Dive into finance management with Free: one essential connection. Perfect for getting started. No credit card required') }}
                    </p>
                    <p class="mt-6 flex items-baseline gap-x-1">
                        <span class="text-4xl font-bold tracking-tight text-gray-900">free</span>
                    </p>
                    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('1 bank connection or 1 crypto wallet or 3 tickers') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited kraken connections') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited cash wallets') }}
                        </li>
                    </ul>
                </div>
                <a
                    href="{{ route('register') }}"
                    wire:navigate
                    aria-describedby="tier-freelancer"
                    class="mt-8 block rounded-md px-3 py-2 text-center text-sm font-semibold leading-6 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:ring-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    {{ __('Get started for free') }}
                </a>
            </div>
            <div
                class="flex flex-col justify-between rounded-3xl bg-white p-8 ring-1 ring-gray-200 lg:z-10 lg:rounded-b-none xl:p-10"
            >
                <div>
                    <div class="flex items-center justify-between gap-x-4">
                        <h3 id="tier-startup" class="text-lg font-semibold leading-8 text-indigo-600">
                            {{ __('Unlimited') }}
                        </h3>
                        <p
                            class="rounded-full bg-indigo-600/10 px-2.5 py-1 text-xs font-semibold leading-5 text-indigo-600"
                        >
                            {{ __('Most popular') }}
                        </p>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">
                        {{ __('Unlock ultimate flexibility with unlimited plan: Unlimited connections, comprehensive control.') }}
                    </p>
                    <p class="mt-6 flex items-baseline gap-x-1">
                        <span class="text-4xl font-bold tracking-tight text-gray-900">
                            {{ $this->unlimitedPrice }} CZK
                        </span>
                        <span class="text-sm font-semibold leading-6 text-gray-600">/month</span>
                    </p>
                    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited bank connection') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited crypto wallet') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited tickers') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited kraken connections') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited cash wallets') }}
                        </li>
                    </ul>
                </div>
                <a
                    href="{{ route('register') }}"
                    wire:navigate
                    aria-describedby="tier-unlimited"
                    class="mt-8 block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    {{ __('Go unlimited') }}
                </a>
            </div>
            <div
                class="flex flex-col justify-between rounded-3xl bg-white p-8 ring-1 ring-gray-200 lg:mt-8 lg:rounded-l-none xl:p-10"
            >
                <div>
                    <div class="flex items-center justify-between gap-x-4">
                        <h3 id="tier-enterprise" class="text-lg font-semibold leading-8 text-gray-900">
                            {{ __('Plus') }}
                        </h3>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">
                        {{ __('Elevate your tracking with plus plan: More connections, broader insights.') }}
                    </p>
                    <p class="mt-6 flex items-baseline gap-x-1">
                        <span class="text-4xl font-bold tracking-tight text-gray-900">{{ $this->plusPrice }} CZK</span>
                        <span class="text-sm font-semibold leading-6 text-gray-600">/month</span>
                    </p>
                    <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('1 bank connection') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('1 crypto wallet') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('15 tickers') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited kraken connections') }}
                        </li>
                        <li class="flex gap-x-3">
                            <x-ts-icon name="check" class="h-6 w-5 flex-none text-indigo-600" />
                            {{ __('Unlimited cash wallets') }}
                        </li>
                    </ul>
                </div>
                <a
                    href="{{ route('register') }}"
                    wire:navigate
                    aria-describedby="tier-plus"
                    class="mt-8 block rounded-md px-3 py-2 text-center text-sm font-semibold leading-6 text-indigo-600 ring-1 ring-inset ring-indigo-200 hover:ring-indigo-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >
                    {{ __('Go plus') }}
                </a>
            </div>
        </div>
    </div>
</div>

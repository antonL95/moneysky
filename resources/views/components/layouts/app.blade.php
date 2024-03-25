<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="bg-base-100"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    {{-- Chart.js  --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    {{--  Currency  --}}
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js"></script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-100">

{{-- The navbar with `sticky` and `full-width` --}}
@auth
    <x-navigation.navigation-menu />
@else
    <x-navigation.guest-navigation-menu />
@endauth

<x-mary-main full-width>
    <x-slot:sidebar drawer="main-drawer" collapsible class="md:pt-20 pb-0 bg-base-200 md:bg-inherit">
        <x-mary-menu activate-by-route>
            <x-mary-menu-item title="{{ __('Overview') }}" icon="fas.chart-pie" link="{{route('app.home')}}"/>
            <x-mary-menu-item title="{{ __('Bank accounts') }}" icon="c-building-library"
                              link="{{route('app.bank-accounts')}}">
                @if(!auth()->user()->subscribed())
                    <x-slot:badge>
                        {{__('All access')}}
                    </x-slot:badge>
                    <x-slot:badgeClasses>
                        bg-accent text-black
                    </x-slot:badgeClasses>
                    <x-slot:noWireNavigate>
                        true
                    </x-slot:noWireNavigate>
                @endif
            </x-mary-menu-item>
            <x-mary-menu-item title="{{ __('Crypto wallets') }}" icon="fab.bitcoin"
                              link="{{route('app.crypto-wallets')}}">
                @if(!auth()->user()->subscribed())
                    <x-slot:badge>
                        {{__('All access')}}
                    </x-slot:badge>
                    <x-slot:badgeClasses>
                        bg-accent text-black
                    </x-slot:badgeClasses>
                    <x-slot:noWireNavigate>
                        true
                    </x-slot:noWireNavigate>
                @endif
            </x-mary-menu-item>
            <x-mary-menu-item title="{{ __('Kraken account') }}" icon="fas.bitcoin-sign"
                              link="{{route('app.kraken-accounts')}}"/>
            <x-mary-menu-item title="{{ __('Stock market') }}" icon="fas.rocket"
                              link="{{route('app.stock-market')}}">
                @if(!auth()->user()->subscribed())
                    <x-slot:badge>
                        {{__('All access')}}
                    </x-slot:badge>
                    <x-slot:badgeClasses>
                        bg-accent text-black
                    </x-slot:badgeClasses>
                    <x-slot:noWireNavigate>
                        true
                    </x-slot:noWireNavigate>
                @endif
            </x-mary-menu-item>
            <x-mary-menu-item title="{{ __('Cash wallets') }}" icon="fas.wallet"
                              link="{{route('app.manual-entries')}}"/>
        </x-mary-menu>
    </x-slot:sidebar>

    <x-slot:content class="pt-20 lg:pt-20">
        {{ $slot }}
    </x-slot:content>
</x-mary-main>

<footer class="text-center p-4">
    @if (isset($footer))
        {{ $footer }}
    @endif
    <p class="text-sm  sm:text-center ">
        © {{now()->format('Y')}} {{ config('app.name') }}. {{__('All rights reserved')}}. {{ __('We appreciate your feedback, please contact us here') }}: <a href="mailto:info@moneysky.app">info@moneysky.app</a></p>
</footer>

{{--  TOAST area --}}
<x-mary-toast/>
@livewireScripts
</body>
</html>

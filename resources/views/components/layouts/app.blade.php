<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkTheme: localStorage.getItem('dark') === 'true',
        toggleTheme() {
            this.darkTheme = !this.darkTheme;
            localStorage.setItem('dark', this.darkTheme);
            Livewire.dispatch('themeToggle', {darkTheme: this.darkTheme});
        },
    }"
    x-init="() => {
        Livewire.dispatch('themeToggle', {darkTheme: localStorage.getItem('dark') === undefined ? false : localStorage.getItem('dark')});
    }"
    x-bind:class="{ 'dark bg-gray-900': darkTheme, 'bg-primary-50': !darkTheme }"
    x-cloak
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <tallstackui:script />
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<x-ts-toast />
    <div class="min-h-screen">
        <header>
            <livewire:navigation.navigation-menu>
            @if (isset($header))
                <div class="container text-center max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            @endif
            @if(Auth::user() !== null && Auth::user()->email_verified_at !== null)
                <livewire:navigation.sidebar-menu/>
            @endif
        </header>

        <main class="max-w-8xl mx-auto px-4 min-h-dvh">
            {{ $slot }}
        </main>


        <footer class="text-center p-4">
            @if (isset($footer))
                {{ $footer }}
            @endif
            <p class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                © {{now()->format('Y')}} {{ config('app.name') }}. All rights reserved.</p>
        </footer>
    </div>

    @stack('modals')

    @livewireScripts
    @livewireChartsScripts
</body>
</html>

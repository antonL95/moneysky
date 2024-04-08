<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="tallstackui_darkTheme()"
    x-bind:class="{ 'dark bg-gray-700': darkTheme, 'bg-white': !darkTheme }"
    class="min-h-dvh"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <tallstackui:script/>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys onSessionId".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
        posthog.init('phc_UfVGHkOeA0iYbKVmaREqfwSpQ5R4RPkXqUNHgSwSYp1', {api_host: "https://eu.posthog.com"})
    </script>
</head>
<body class="font-sans antialiased flex flex-col min-h-dvh">
<x-ts-toast />
<x-navigation.navigation-menu>
    {{$slot}}
</x-navigation.navigation-menu>

<!-- Footer -->
<footer aria-labelledby="footer-heading" class="mt-auto">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="mx-auto max-w-7xl px-6 pb-8 lg:px-8">
        <div class="border-t border-white/10 pt-8 sm:mt-20 md:flex md:items-center md:justify-center lg:mt-24">
            <p class="mt-8 text-xs leading-5 text-gray-400 md:order-1 md:mt-0">
                &copy; {{now()->format('Y')}} {{ config('app.name') }}. {{__('All rights reserved')}}
                . {{ __('We appreciate your feedback, please contact us here') }}: <a href="mailto:info@moneysky.app">info@moneysky.app</a>
            </p>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>

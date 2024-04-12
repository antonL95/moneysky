<script>
    function setConsentCookie() {
        const d = new Date();
        const name = 'cookie-consent';
        d.setTime(d.getTime() + 10 * 365 * 24 * 60 * 60 * 1000);
        document.cookie = `name=${name};expires=${d.toUTCString()};domain={{ config('session.domain') }};path=/{{ config('session.secure') ? ';secure' : '' }}{{ config('session.same_site') ? ';samesite' : '' }}`;
    }

    function userDidConsent() {
        return document.cookie.split(';').indexOf(`name=cookie-consent`) !== -1;
    }
</script>

<div
    aria-live="assertive"
    class="fixed inset-x-1 bottom-0 left-0 flex items-end px-4 py-6 sm:items-start sm:p-6"
    x-cloak
    x-data="{
        open: ! userDidConsent(),
        toggle() {
            this.open = ! this.open
            setConsentCookie()
        },
    }"
>
    <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
        <div
            x-show="open"
            x-transition:enter="transform transition duration-300 ease-out"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-x-2 sm:translate-y-0"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">🍪 {{ __('Cookie consent') }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            {!!
                                __('The only cookies we collect are those that we need to keep you logged in! We don\'t have third-party cookies if you want to know more please read :terms_of_service', [
                                    'terms_of_service' => '<a target="_blank" href="' . route('terms.show') . '" class="underline text-sm  dark:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">' . __('Terms of Service') . '</a>',
                                ])
                            !!}
                        </p>
                        <div class="mt-4 flex">
                            <button
                                x-on:click="toggle()"
                                type="button"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            >
                                {{ __('Accept') }}
                            </button>
                        </div>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            type="button"
                            x-on:click="toggle()"
                            class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

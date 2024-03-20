<nav class="px-4 py-2.5 fixed left-0 right-0 top-0 z-20">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-start items-center">
            @auth
                <label for="main-drawer" class="lg:hidden mr-5">
                    <svg aria-hidden="true"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg aria-hidden="true"
                        class="hidden w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">Toggle sidebar</span>
                </label>
            @endauth
            <a href="{{Auth::user() ? route('app.home') : route('home')}}"
               wire:navigate
               class="flex items-center justify-between mr-4">
                <x-application-logo class="w-10 h-10 mr-2 fill-dark-900 stroke-dark-900 dark:stroke-white dark:fill-white" />
                <span class="hidden sm:inline-flex self-center text-2xl font-semibold whitespace-nowrap dark:text-white">
                    {{ config('app.name') }}
                </span>
            </a>
        </div>
        <div class="flex items-center lg:order-2">
            @auth
                <button type="button"
                    class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                    id="user-menu-button"
                    aria-expanded="false"
                    data-dropdown-toggle="dropdown">
                    <span class="sr-only">Open user menu</span>
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="w-8 h-8 rounded-full"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ Auth::user()->name }}">
                    @else
                        <span class="inline-flex rounded-md">
                            <x-mary-avatar />
                        </span>
                    @endif
                </button>

                <!-- Dropdown menu -->
                <div
                    class="hidden z-50 my-4 w-56 text-base list-none bg-primary-50 rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 overflow-hidden"
                    id="dropdown">
                    <div class="py-3 px-4">
                        <span class="block text-sm font-semibold text-gray-900 dark:text-white">
                          {{ Auth::user()->name }}
                        </span>
                        <span class="block text-sm text-gray-900 truncate dark:text-white">
                            {{ Auth::user()->email }}
                        </span>
                    </div>
                    <ul class="py-1 text-gray-700 dark:text-gray-300"
                        aria-labelledby="dropdown">
                        <li>
                            <a href="{{route('profile.show')}}" wire:navigate
                               class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                {{__('My profile')}}
                            </a>
                        </li>
                        <li>
                            <a href="@if(auth()->user()->subscribed()) {{route('billing')}} @else {{route('subscription-checkout', ['plan' => 'monthly'])}} @endif"
                               class="block py-2 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white">
                                {{__('Billing')}}
                            </a>
                        </li>
                    </ul>
                    <ul class="pt-1 text-gray-700 dark:text-gray-300"
                        aria-labelledby="dropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <a href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();"
                                   class="block pt-2 pb-3 px-4 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    {{ __('Sign out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <x-mary-button class="btn-primary" link="{{route('login')}}" wire:navigate
                   class="mx-2">
                    {{__('Log in')}}
                </x-mary-button>
                <x-mary-button class="btn-primary" link="{{route('register')}}"
                   class="btn-primary">
                    {{__('Start')}}
                </x-mary-button>
            @endauth
            <!-- Dark Mode -->
{{--            <livewire:dark-mode-switcher />--}}
                <x-mary-theme-toggle class="ml-4" />
        </div>
    </div>
</nav>

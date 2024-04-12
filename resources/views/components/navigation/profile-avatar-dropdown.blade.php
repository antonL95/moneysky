<div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
    <div class="ml-auto flex items-center gap-x-4 lg:gap-x-6">
        <div class="relative">
            <x-ts-dropdown>
                <x-slot:action>
                    <button
                        type="button"
                        class="-m-1.5 flex items-center p-1.5"
                        id="user-menu-button"
                        aria-expanded="false"
                        aria-haspopup="true"
                        x-on:click="show = !show"
                    >
                        <span class="sr-only">Open user menu</span>
                        <x-ts-avatar sm :model="auth()->user()" color="white" />
                    </button>
                </x-slot>
                <a href="{{ route('profile.show') }}" wire:navigate>
                    <x-ts-dropdown.items>
                        <x-slot:text>
                            {{ __('Profile') }}
                        </x-slot>
                    </x-ts-dropdown.items>
                </a>

                @if (! auth()->user()->demo)
                    @if (! auth()->user()->checkSubscriptionType('plus') &&! auth()->user()->checkSubscriptionType('unlimited'))
                        <a href="{{ route('subscription-checkout', ['plan' => 'unlimited']) }}">
                            <x-ts-dropdown.items>
                                <x-slot:text>
                                    {{ __('Subscribe to unlimited') }}
                                </x-slot>
                            </x-ts-dropdown.items>
                        </a>
                        <a href="{{ route('subscription-checkout', ['plan' => 'plus']) }}">
                            <x-ts-dropdown.items>
                                <x-slot:text>
                                    {{ __('Subscribe to plus') }}
                                </x-slot>
                            </x-ts-dropdown.items>
                        </a>
                    @elseif (auth()->user()->checkSubscriptionType('plus') &&! auth()->user()->checkSubscriptionType('unlimited'))
                        <a href="{{ route('billing') }}">
                            <x-ts-dropdown.items>
                                <x-slot:text>
                                    {{ __('Upgrade to unlimited') }}
                                </x-slot>
                            </x-ts-dropdown.items>
                        </a>
                        <a href="{{ route('billing') }}">
                            <x-ts-dropdown.items>
                                <x-slot:text>
                                    {{ __('Manage subscription') }}
                                </x-slot>
                            </x-ts-dropdown.items>
                        </a>
                    @else
                        <a href="{{ route('billing') }}">
                            <x-ts-dropdown.items>
                                <x-slot:text>
                                    {{ __('Manage subscription') }}
                                </x-slot>
                            </x-ts-dropdown.items>
                        </a>
                    @endif
                @endif

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <a href="{{ route('logout') }}" no-wire-navigate @click.prevent="$root.submit();">
                        <x-ts-dropdown.items>
                            <x-slot:text>
                                {{ __('Sign out') }}
                            </x-slot>
                        </x-ts-dropdown.items>
                    </a>
                </form>
            </x-ts-dropdown>
        </div>
    </div>
</div>

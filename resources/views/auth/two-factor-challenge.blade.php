<x-guest-layout>
    <x-authentication-card>
        <x-slot:headline>
            {{ __('Two factor authentication') }}
        </x-slot:headline>

        <div x-data="{ recovery: false }">
            <div class="mb-4 text-sm" x-show="! recovery">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </div>

            <div class="mb-4 text-sm" x-cloak x-show="recovery">
                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
            </div>

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mt-4" x-show="! recovery">
                    <x-ts-input id="recovery_code" label="{{ __('Code') }}" class="block mt-1 w-full" autofocus
                                type="text" name="code" x-ref="code" autocomplete="one-time-code"/>
                </div>

                <div class="mt-4" x-cloak x-show="recovery">
                    <x-ts-input id="code" label="{{ __('Code') }}" class="block mt-1 w-full" type="text"
                                name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code"/>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-button type="button" class="text-sm underline cursor-pointer"
                              :title="__('Use a recovery code')"
                              x-show="! recovery"
                              x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    "/>

                    <x-button type="button" class="text-sm underline cursor-pointer btn"
                              x-cloak
                              :title="__('Use an authentication code')"
                              x-show="recovery"
                              x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    "/>

                    <x-button type="submit" class="btn btn-primary ms-4" :title="__('Log in')"/>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>

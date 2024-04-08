<x-guest-layout>
    <x-authentication-card>
        <x-slot:headline>
            {{__('Sign in to your account')}}
        </x-slot:headline>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-6">
            @csrf
            <x-ts-input type="email" autocomplete="email"
                        name="email" :value="old('email')" required
                        placeholder="example@email.org" label="{{__('Email')}}"/>
            <x-ts-input type="password" autocomplete="password"
                        name="password" :value="old('password')"
                        required placeholder="password"/>

            <div class="flex items-center justify-between">
                <x-ts-checkbox name="remember" label="{{ __('Remember me') }}" id="remember"
                               :checked="old('remember')"/>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-medium -600 hover:underline">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
            <x-button
                title="{{ __('Log in') }}" type="submit"
                class="w-full"
            />
            <p class="text-sm font-light">
                {{__('Don’t have an account yet?')}}
                <a href="{{ route('register') }}"
                   class="font-medium -600 hover:underline 0">{{__('Sign up')}}</a>
            </p>
        </form>
    </x-authentication-card>
</x-guest-layout>

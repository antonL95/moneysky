<x-guest-layout>
    <x-authentication-card>
        <x-slot:headline>
            {{ __('Reset password') }}
        </x-slot:headline>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-ts-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus label="{{ __('Email') }}" />

            <div class="mt-4">
                <x-ts-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" label="{{ __('Password') }}" />
            </div>

            <div class="mt-4">
                <x-ts-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" label="{{ __('Confirm Password') }}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit" :title="__('Reset Password')"/>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

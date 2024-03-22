<x-layouts.guest>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <x-mary-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus label="{{ __('Email') }}" />

            <div class="mt-4">
                <x-mary-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" label="{{ __('Password') }}" />
            </div>

            <div class="mt-4">
                <x-mary-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" label="{{ __('Confirm Password') }}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-mary-button type="submit" class="btn btn-primary">
                    {{ __('Reset Password') }}
                </x-mary-button>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>

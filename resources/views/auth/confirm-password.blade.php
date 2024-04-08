<x-layouts.guest>
    <x-authentication-card>
        <x-slot:headline>
            {{ __('Confirm password') }}
        </x-slot:headline>

        <div class="mb-4 text-sm">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-ts-input id="password" class="block mt-1 w-full" type="password" name="password" required
                              autocomplete="current-password" autofocus label="{{ __('Password') }}"/>
            </div>

            <div class="flex justify-end mt-4">
                <x-button type="submit" class="ms-4" :title="__('Confirm')"/>
            </div>
        </form>
    </x-authentication-card>
</x-layouts.guest>

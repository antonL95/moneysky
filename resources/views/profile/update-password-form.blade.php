<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <x-mary-input label="{{ __('Current Password') }}" wire:model="state.current_password" type="password" required autocomplete="current-password" />
        <x-mary-input label="{{ __('New Password') }}" wire:model="state.password" type="password" required autocomplete="new-password" />
        <x-mary-input label="{{ __('Confirm Password') }}" wire:model="state.password_confirmation" type="password" required autocomplete="new-password" />
    </x-slot>

    <x-slot name="actions">
        <x-mary-button type="submit" class="btn btn-primary">
            {{ __('Save') }}
        </x-mary-button>
    </x-slot>
</x-form-section>

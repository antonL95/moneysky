<x-form-section submit="updatePassword">
    <x-slot:title>
        {{ __('Update Password') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot:description>

    <x-slot:form>
        <x-ts-input label="{{ __('Current Password') }}" wire:model="state.current_password" type="password" required autocomplete="current-password" />
        <x-ts-input label="{{ __('New Password') }}" wire:model="state.password" type="password" required autocomplete="new-password" />
        <x-ts-input label="{{ __('Confirm Password') }}" wire:model="state.password_confirmation" type="password" required autocomplete="new-password" />
    </x-slot:form>

    <x-slot:actions>
        <div class="mt-2">
            <x-button type="submit" :title="__('Save')" />
        </div>
    </x-slot:actions>
</x-form-section>

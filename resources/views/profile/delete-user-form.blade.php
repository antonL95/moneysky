<x-action-section>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </div>

        <div class="mt-5">
            <x-mary-button class=" btn bg-red-600 hover:bg-red-500" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Delete Account') }}
            </x-mary-button>
        </div>

        <x-mary-modal wire:model.live="confirmingUserDeletion">
            <x-slot:title>
                {{ __('Delete Account') }}
            </x-slot:title>
            {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}

            <div class="mt-4" x-data="{}"
                 x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                <x-mary-input type="password" class="mt-1 block w-3/4"
                              autocomplete="current-password"
                              placeholder="{{ __('Password') }}"
                              x-ref="password"
                              wire:model="password"
                              wire:keydown.enter="deleteUser"/>
            </div>

            <x-slot:actions>
                <x-mary-button class="btn" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-mary-button>

                <x-mary-button type="submit" class="ms-3  bg-red-600 hover:bg-red-500" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Delete Account') }}
                </x-mary-button>
            </x-slot:actions>
        </x-mary-modal>
    </x-slot>
</x-action-section>

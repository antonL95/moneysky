<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <x-mary-file label="Profile photo" wire:model="photo" accept="image/png, image/jpeg">
                <img src="{{ $this->user->profile_photo_url ?? '/empty-user.jpg' }}" alt="{{ $this->user->name }}" class="h-40 rounded-full" />
            </x-mary-file>
        @endif
        <div>
            <x-mary-input label="{{ __('Name') }}" wire:model="state.name" required/>
            <x-mary-input label="{{ __('Email') }}" wire:model="state.email" type="email" required/>
        </div>
    </x-slot>

    <x-slot:actions>
        <div>
            <x-mary-button wire:loading.attr="disabled" wire:target="photo" class="btn btn-primary" type="submit">
                {{ __('Save') }}
            </x-mary-button>
        </div>
    </x-slot:actions>
</x-form-section>

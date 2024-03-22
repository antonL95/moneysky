@props(['title' => __('Confirm Password'), 'content' => __('For your security, please confirm your password to continue.'), 'button' => __('Confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
<x-mary-modal wire:model.live="confirmingPassword">
    <x-slot:title>
        {{ $title }}
    </x-slot:title>

    {{ $content }}

    <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
        <x-mary-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}" autocomplete="current-password"
                    x-ref="confirmable_password"
                    wire:model="confirmablePassword"
                    wire:keydown.enter="confirmPassword" />
    </div>

    <x-slot:actions>
        <x-mary-button class="btn" wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
            {{ __('Cancel') }}
        </x-mary-button>

        <x-mary-button class="ms-3 btn btn-primary" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
            {{ $button }}
        </x-mary-button>
    </x-slot:actions>
</x-mary-modal>
@endonce

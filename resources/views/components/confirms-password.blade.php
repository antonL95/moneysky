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
<x-ts-modal wire="confirmingPassword">
    <x-slot:title>
        {{ $title }}
    </x-slot:title>

    {{ $content }}

    <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
        <x-ts-input type="password" class="mt-1 block w-3/4" placeholder="{{ __('Password') }}" autocomplete="current-password"
                    x-ref="confirmable_password"
                    wire:model="confirmablePassword"
                    wire:keydown.enter="confirmPassword" />
    </div>

    <div class="mt-2">
        <x-button class="bg-dark-400" wire:click="$toggle(confirmingPassword)" wire:loading.attr="disabled" :title="__('Cancel')"/>
        <x-button class="ms-3" :title="$button" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled"/>
    </div>
</x-ts-modal>
@endonce

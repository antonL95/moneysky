<x-button x-on:click="$modalOpen('{{ $modal }}')" class="mb-3">
    <x-slot:title>
        <x-ts-icon name="plus" class="w-6 h-6 pr-2"/>
        {{ $title ?? "Add" }}
    </x-slot:title>
</x-button>

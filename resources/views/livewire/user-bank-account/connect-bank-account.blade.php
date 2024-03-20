@php use App\Bank\Models\BankInstitution; use Illuminate\Support\Facades\Cache; @endphp
<x-mary-form class="max-w-sm mx-auto min-h-[450px] md:min-h-[600px]" wire:submit="connect">
    <x-mary-choices
        label="Choose your bank"
        wire:model="institution"
        :options="$institutionsSearchable"
        single
        option-avatar="image"
        option-sub-label="countries"
        searchable/>
    <x-slot:actions>
        <x-mary-button type="submit" class="btn-primary">
            {{ __('Connect') }}
        </x-mary-button>
    </x-slot:actions>
</x-mary-form>

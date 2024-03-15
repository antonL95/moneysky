@php use App\Bank\Models\BankInstitution; use Illuminate\Support\Facades\Cache; @endphp
<div>
    <form class="max-w-sm mx-auto" wire:submit="connect">
        <x-ts-select.styled
            :options="Cache::get('bank-institutions')->map(fn($institution) => ['label' => $institution->name . '('.implode(',', $institution->countries).')', 'value' => $institution->id])"
            select="label:label|value:value"
            searchable wire:model="institution" required/>
        <x-button class="mt-2" type="submit">Connect</x-button>
    </form>
</div>

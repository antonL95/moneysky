@php use App\Bank\Models\BankInstitution; use Illuminate\Support\Facades\Cache; @endphp
<div>
    <form class="max-w-sm mx-auto" wire:submit="connect">
        <x-ts-select.styled
            :request="[
                'url' => route('app.list-institutions'),
                'method' => 'get',
            ]"
            select="label:name|value:id"
            searchable wire:model="institution" required/>
        <x-button class="mt-2" type="submit">
            {{ __('Connect') }}
        </x-button>
    </form>
</div>

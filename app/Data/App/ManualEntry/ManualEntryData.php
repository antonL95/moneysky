<?php

declare(strict_types=1);

namespace App\Data\App\ManualEntry;

use App\Helpers\CurrencyHelper;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class ManualEntryData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float|int $balance,
        public string $currency,
    ) {}

    /**
     * @return array<string, In>
     */
    public static function rules(): array
    {
        return ['currency' => Rule::in(CurrencyHelper::getCurrencies())];
    }
}

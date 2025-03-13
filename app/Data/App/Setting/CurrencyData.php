<?php

declare(strict_types=1);

namespace App\Data\App\Setting;

use App\Helpers\CurrencyHelper;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MergeValidationRules]
final class CurrencyData extends Data
{
    public function __construct(
        public readonly string $currency,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'currency' => [Rule::in(CurrencyHelper::getCurrencies())],
        ];
    }
}

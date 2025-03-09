<?php

declare(strict_types=1);

namespace App\Data;

use App\Helpers\CurrencyHelper;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MergeValidationRules]
final class TransactionData extends Data
{
    public function __construct(
        public float|int $balance,
        public string $currency,
        public ?string $description,
        public ?int $transaction_tag_id,
        public ?int $user_manual_entry_id,
    ) {}

    /**
     * @return array<string, array<string|In>>
     */
    public static function rules(): array
    {
        return [
            'currency' => ['required', Rule::in(CurrencyHelper::getCurrencies())],
        ];
    }
}

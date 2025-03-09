<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use App\Models\TransactionTag;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MergeValidationRules]
final class BudgetData extends Data
{
    /**
     * @param  array<int, TransactionTag>|null  $tags
     */
    public function __construct(
        public string $name,
        public float|int $balance,
        public string $currency,
        public ?array $tags,
    ) {}

    /**
     * @return array<string, array<string|Exists>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'tags' => ['nullable', Rule::exists(TransactionTag::class, 'id')],
        ];
    }
}

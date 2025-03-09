<?php

declare(strict_types=1);

namespace App\Data\App\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class UserBudgetData extends Data
{
    /**
     * @param  int[]  $tags
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $spent,
        public readonly float $budget,
        public readonly string $currency,
        public readonly array $tags,
        public readonly int $budgetId,
    ) {}
}

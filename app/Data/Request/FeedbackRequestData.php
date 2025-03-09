<?php

declare(strict_types=1);

namespace App\Data\Request;

use App\Enums\FeedbackType;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Data;

final class FeedbackRequestData extends Data
{
    public function __construct(
        public readonly FeedbackType $type,
        public readonly string $description,
    ) {}

    /**
     * @return array<string, array<int, Enum|string>>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(FeedbackType::class)],
            'description' => ['required'],
        ];
    }
}

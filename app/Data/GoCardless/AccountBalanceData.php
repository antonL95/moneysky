<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Spatie\LaravelData\Dto;

final class AccountBalanceData extends Dto
{
    public function __construct(
        public int $balance,
    ) {}

    /**
     * @param  array<string, array<string, float>>  $data
     */
    public static function fromMultiple(array $data): self
    {
        return new self(
            (int) ($data['balanceAmount']['amount'] * 100),
        );
    }
}

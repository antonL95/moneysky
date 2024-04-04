<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

final readonly class BankBalanceDto
{
    /**
     * @param array<string, array<string, float>> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['balanceAmount']['amount'] * 100),
        );
    }

    public function __construct(
        public int $balance,
    ) {
    }
}

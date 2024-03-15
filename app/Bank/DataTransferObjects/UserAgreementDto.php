<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Exceptions\InvalidApiException;

readonly class UserAgreementDto
{
    /**
     * @param array<string, string|int|string[]> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'];
        $maxHistoricalDays = $data['max_historical_days'];
        $accessValidForDays = $data['access_valid_for_days'];
        $accessScope = $data['access_scope'];

        if (!\is_string($id) || !is_numeric($maxHistoricalDays) || !is_numeric($accessValidForDays) || !\is_array($accessScope)) {
            throw InvalidApiException::invalidDataEntry();
        }

        return new self(
            $id,
            (int) $maxHistoricalDays,
            (int) $accessValidForDays,
            $accessScope,
        );
    }

    /**
     * @param string[] $accessScope
     */
    public function __construct(
        public string $id,
        public int $maxHistoricalDays,
        public int $accessValidForDays,
        public array $accessScope,
    ) {
    }
}

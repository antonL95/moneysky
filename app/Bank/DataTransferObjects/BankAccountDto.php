<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Exceptions\InvalidApiException;

readonly class BankAccountDto
{
    /**
     * @param array<string, string|int|string[]|null> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'];
        $currency = $data['currency'];

        if (!\is_string($id) || !\is_string($currency)) {
            throw InvalidApiException::invalidDataEntry();
        }

        $name = $data['name'];

        if ($name === null) {
            $name = null;
        } elseif (!\is_string($name)) {
            throw InvalidApiException::invalidDataEntry();
        }

        $iban = $data['iban'];

        if ($iban === null) {
            $iban = null;
        } elseif (!\is_string($iban)) {
            throw InvalidApiException::invalidDataEntry();
        }

        $resourceId = $data['resourceId'];

        if ($resourceId === null) {
            $resourceId = null;
        } elseif (!\is_string($resourceId)) {
            throw InvalidApiException::invalidDataEntry();
        }

        return new self(
            $id,
            $currency,
            $name,
            $iban,
            $resourceId,
        );
    }

    public function __construct(
        public string $id,
        public string $currency,
        public ?string $name,
        public ?string $iban,
        public ?string $resourceId,
    ) {
    }
}
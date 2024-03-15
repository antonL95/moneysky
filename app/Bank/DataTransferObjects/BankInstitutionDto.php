<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Exceptions\InvalidApiException;

use function Safe\json_encode;

readonly class BankInstitutionDto
{
    /**
     * @param array<string, string|int|string[]> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'];
        $name = $data['name'];
        $bic = $data['bic'];
        $transactionTotalDays = $data['transaction_total_days'];
        $countries = $data['countries'];
        $logo = $data['logo'];

        if (!\is_string($id) || !\is_string($name) || !\is_string($bic) || !is_numeric($transactionTotalDays) || !\is_array($countries) || !\is_string($logo)) {
            throw InvalidApiException::invalidDataEntry(json_encode($data));
        }

        return new self(
            $id,
            $name,
            $bic,
            (int) $transactionTotalDays,
            $countries,
            $logo,
        );
    }

    /**
     * @param string[] $countries
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $bic,
        public int $transactionTotalDays,
        public array $countries,
        public string $logoUrl,
    ) {
    }
}

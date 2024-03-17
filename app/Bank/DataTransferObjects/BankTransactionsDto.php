<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Exceptions\InvalidApiException;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

readonly class BankTransactionsDto
{
    /**
     * @param array<string, string|float|int|array<string, string|numeric>|null> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $externalId = $data['externalId'] ?? Uuid::uuid4()->toString();
        $balanceAmount = $data['transactionAmount'];

        if (!\is_array($balanceAmount) && !isset($balanceAmount['amount'], $balanceAmount['currency'])) {
            throw InvalidApiException::invalidDataEntry();
        }

        $balance = $balanceAmount['amount'];
        $currency = $balanceAmount['currency'];
        $currencyExchange = $data['currencyExchange'] ?? null;
        $additionalInformation = $data['additionalInformation'] ?? null;
        $remittanceInformation = $data['remittanceInformationUnstructured'] ?? null;

        if (!\is_string($externalId) || !is_numeric($balance) || !\is_string($currency)) {
            throw InvalidApiException::invalidDataEntry();
        }

        if ($currencyExchange !== null && !\is_array($currencyExchange)) {
            throw InvalidApiException::invalidDataEntry();
        }

        if ($additionalInformation !== null && !\is_string($additionalInformation)) {
            throw InvalidApiException::invalidDataEntry();
        }

        if ($remittanceInformation !== null && !\is_string($remittanceInformation)) {
            throw InvalidApiException::invalidDataEntry();
        }

        if (isset($data['bookingDate']) && \is_string($data['bookingDate'])) {
            $bookingDate = new Carbon($data['bookingDate']);
        }

        if (isset($data['bookingDateTime']) && \is_string($data['bookingDateTime'])) {
            $bookingDateTime = new Carbon($data['bookingDateTime']);
        }

        return new self(
            $externalId,
            (int) floor($balance * 100),
            $currency,
            $currencyExchange,
            $additionalInformation,
            $remittanceInformation,
            $bookingDate ?? null,
            $bookingDateTime ?? null,
        );
    }

    /**
     * @param array<string, mixed> $currencyExchange
     */
    public function __construct(
        public string $externalId,
        public int $balance,
        public string $currency,
        public ?array $currencyExchange,
        public ?string $additionalInformation,
        public ?string $remittanceInformation,
        public ?Carbon $bookingDate,
        public ?Carbon $bookingDateTime,
    ) {
    }
}

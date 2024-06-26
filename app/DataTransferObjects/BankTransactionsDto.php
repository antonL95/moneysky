<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Exceptions\InvalidApiException;
use Illuminate\Support\Carbon;

use function Safe\json_encode;

final readonly class BankTransactionsDto
{
    /**
     * @param array<string, string|float|int|array<string, string|numeric>|null> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $externalId = $data['entryReference'] ?? $data['internalTransactionId'];
        $balanceAmount = $data['transactionAmount'];

        if (!\is_array($balanceAmount) && !isset($balanceAmount['amount'], $balanceAmount['currency'])) {
            throw InvalidApiException::invalidDataEntry();
        }

        $balance = $balanceAmount['amount'];
        $currency = $balanceAmount['currency'];
        $currencyExchange = $data['currencyExchange'] ?? null;
        $additionalInformation = $data['additionalInformation'] ?? null;
        $unstructuredArray = $data['remittanceInformationUnstructuredArray'] ?? null;

        $unstructuredInformation = null;

        if ($unstructuredArray !== null) {
            $unstructuredInformation = json_encode($unstructuredArray);
        }

        $remittanceInformation = $data['remittanceInformationUnstructured'] ?? $unstructuredInformation;

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

        if (isset($data['merchantCategoryCode']) && \is_string($data['merchantCategoryCode'])) {
            $merchantCategoryCode = $data['merchantCategoryCode'];
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
            $merchantCategoryCode ?? null,
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
        public ?string $merchantCategoryCode = null,
    ) {
    }
}

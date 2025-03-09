<?php

declare(strict_types=1);

namespace App\Data\GoCardless;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Dto;

final class AccountTransactionsData extends Dto
{
    #[Computed]
    public int $balance;

    public string $currency;

    #[Computed]
    public ?CarbonImmutable $booking = null;

    #[Computed]
    public ?CarbonImmutable $bookingTime = null;

    public function __construct(
        public ?string $entryReference,
        public string $internalTransactionId,
        /** @var array<string, string|numeric> */
        public array $transactionAmount,
        /** @var array<string, string|numeric>|null */
        public ?array $currencyExchange = null,
        public ?string $additionalInformation = null,
        public ?string $remittanceInformationUnstructured = null,
        ?string $bookingDate = null,
        ?string $valueDate = null,
        public ?string $merchantCategoryCode = null,
    ) {
        $this->balance = (int) ((float) $transactionAmount['amount'] * 100);
        $this->currency = (string) $transactionAmount['currency'];

        if ($bookingDate !== null) {
            $this->booking = CarbonImmutable::parse($bookingDate);
        }

        if ($valueDate !== null) {
            $this->bookingTime = CarbonImmutable::parse($valueDate);
        }
    }
}

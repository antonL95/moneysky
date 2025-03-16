<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Resource;

use App\Http\Integrations\GoCardless\Requests\Institutions\RetrieveAllSupportedInstitutionsInGivenCountry;
use App\Http\Integrations\GoCardless\Resource;
use Saloon\Http\Response;

final class Institutions extends Resource
{
    public function retrieveAllSupportedInstitutionsInGivenCountry(
        ?bool $accessScopesSupported = null,
        ?bool $accountSelectionSupported = null,
        ?bool $businessAccountsSupported = null,
        ?bool $cardAccountsSupported = null,
        ?bool $corporateAccountsSupported = null,
        ?string $country = null,
        ?bool $paymentSubmissionSupported = null,
        ?bool $paymentsEnabled = null,
        ?bool $pendingTransactionsSupported = null,
        ?bool $privateAccountsSupported = null,
        ?bool $readDebtorAccountSupported = null,
        ?bool $readRefundAccountSupported = null,
        ?bool $ssnVerificationSupported = null,
    ): Response {
        return $this->connector->send(
            new RetrieveAllSupportedInstitutionsInGivenCountry(
                $accessScopesSupported,
                $accountSelectionSupported,
                $businessAccountsSupported,
                $cardAccountsSupported,
                $corporateAccountsSupported,
                $country,
                $paymentSubmissionSupported,
                $paymentsEnabled,
                $pendingTransactionsSupported,
                $privateAccountsSupported,
                $readDebtorAccountSupported,
                $readRefundAccountSupported,
                $ssnVerificationSupported,
            ),
        );
    }
}

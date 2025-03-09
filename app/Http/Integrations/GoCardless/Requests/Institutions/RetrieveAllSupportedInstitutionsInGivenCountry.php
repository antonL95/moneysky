<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Institutions;

use App\Data\GoCardless\InstitutionsData;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class RetrieveAllSupportedInstitutionsInGivenCountry extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?bool $accessScopesSupported = null,
        private readonly ?bool $accountSelectionSupported = null,
        private readonly ?bool $businessAccountsSupported = null,
        private readonly ?bool $cardAccountsSupported = null,
        private readonly ?bool $corporateAccountsSupported = null,
        private readonly ?string $country = null,
        private readonly ?bool $paymentSubmissionSupported = null,
        private readonly ?bool $paymentsEnabled = null,
        private readonly ?bool $pendingTransactionsSupported = null,
        private readonly ?bool $privateAccountsSupported = null,
        private readonly ?bool $readDebtorAccountSupported = null,
        private readonly ?bool $readRefundAccountSupported = null,
        private readonly ?bool $ssnVerificationSupported = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/institutions/';
    }

    public function defaultQuery(): array
    {
        return array_filter([
            'access_scopes_supported' => $this->accessScopesSupported,
            'account_selection_supported' => $this->accountSelectionSupported,
            'business_accounts_supported' => $this->businessAccountsSupported,
            'card_accounts_supported' => $this->cardAccountsSupported,
            'corporate_accounts_supported' => $this->corporateAccountsSupported,
            'country' => $this->country,
            'payment_submission_supported' => $this->paymentSubmissionSupported,
            'payments_enabled' => $this->paymentsEnabled,
            'pending_transactions_supported' => $this->pendingTransactionsSupported,
            'private_accounts_supported' => $this->privateAccountsSupported,
            'read_debtor_account_supported' => $this->readDebtorAccountSupported,
            'read_refund_account_supported' => $this->readRefundAccountSupported,
            'ssn_verification_supported' => $this->ssnVerificationSupported,
        ]);
    }

    /**
     * @return Collection<int, InstitutionsData>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return InstitutionsData::collect($response->array(), Collection::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Requests\Requisitions;

use App\Data\GoCardless\RequisitionData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Create requisition
 * Create a new requisition
 */
final class CreateRequisition extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $institutionId,
        public string $agreementId,
        public string $redirect,
        public ?string $reference = null,
        public ?string $userLanguage = null,
        public ?string $ssn = null,
        public ?bool $accountSelection = null,
        public ?bool $redirectImmediate = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/v2/requisitions/';
    }

    public function createDtoFromResponse(Response $response): RequisitionData
    {
        return RequisitionData::from($response->array());
    }

    /**
     * @return array<string, string|bool|null>
     */
    protected function defaultBody(): array
    {
        $body = [
            'institution_id' => $this->institutionId,
            'agreement' => $this->agreementId,
            'redirect' => $this->redirect,
        ];

        if ($this->reference !== null) {
            $body['reference'] = $this->reference;
        }
        if ($this->userLanguage !== null) {
            $body['user_language'] = $this->userLanguage;
        }
        if ($this->ssn !== null) {
            $body['ssn'] = $this->ssn;
        }
        if ($this->accountSelection !== null) {
            $body['account_selection'] = $this->accountSelection;
        }
        if ($this->redirectImmediate !== null) {
            $body['redirect_immediate'] = $this->redirectImmediate;
        }

        return $body;
    }
}

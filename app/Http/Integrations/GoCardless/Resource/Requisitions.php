<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Resource;

use App\Http\Integrations\GoCardless\Requests\Requisitions\CreateRequisition;
use App\Http\Integrations\GoCardless\Requests\Requisitions\DeleteRequisitionById;
use App\Http\Integrations\GoCardless\Requests\Requisitions\RequisitionById;
use App\Http\Integrations\GoCardless\Requests\Requisitions\RetrieveAllRequisitions;
use App\Http\Integrations\GoCardless\Resource;
use Saloon\Http\Response;

final class Requisitions extends Resource
{
    public function retrieveAllRequisitions(?int $limit, ?int $offset): Response
    {
        return $this->connector->send(new RetrieveAllRequisitions($limit, $offset));
    }

    public function createRequisition(
        string $institutionId,
        string $agreementId,
        string $redirect,
        ?string $reference = null,
        ?string $userLanguage = null,
        ?string $ssn = null,
        ?bool $accountSelection = null,
        ?bool $redirectImmediate = null,
    ): Response {
        return $this->connector->send(
            new CreateRequisition(
                $institutionId,
                $agreementId,
                $redirect,
                $reference,
                $userLanguage,
                $ssn,
                $accountSelection,
                $redirectImmediate,
            ),
        );
    }

    public function requisitionById(string $id): Response
    {
        return $this->connector->send(new RequisitionById($id));
    }

    public function deleteRequisitionById(string $id): Response
    {
        return $this->connector->send(new DeleteRequisitionById($id));
    }
}

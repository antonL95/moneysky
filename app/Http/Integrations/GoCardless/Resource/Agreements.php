<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Resource;

use App\Http\Integrations\GoCardless\Requests\Agreements\AcceptEua;
use App\Http\Integrations\GoCardless\Requests\Agreements\CreateEua;
use App\Http\Integrations\GoCardless\Requests\Agreements\DeleteEuaById;
use App\Http\Integrations\GoCardless\Requests\Agreements\RetrieveAllEuasForEndUser;
use App\Http\Integrations\GoCardless\Requests\Agreements\RetrieveEuaById;
use App\Http\Integrations\GoCardless\Resource;
use Saloon\Http\Response;

final class Agreements extends Resource
{
    public function retrieveAllEuasForEndUser(?int $limit, ?int $offset): Response
    {
        return $this->connector->send(new RetrieveAllEuasForEndUser($limit, $offset));
    }

    public function createEua(
        string $institutionId,
        int $maxHistoricalDays = 90,
        int $accessValidForDays = 90,
    ): Response {
        return $this->connector->send(new CreateEua($institutionId, $maxHistoricalDays, $accessValidForDays));
    }

    public function retrieveEuaById(string $id): Response
    {
        return $this->connector->send(new RetrieveEuaById($id));
    }

    public function deleteEuaById(string $id): Response
    {
        return $this->connector->send(new DeleteEuaById($id));
    }

    public function acceptEua(string $id): Response
    {
        return $this->connector->send(new AcceptEua($id));
    }
}

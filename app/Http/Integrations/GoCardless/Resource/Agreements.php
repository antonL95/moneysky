<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless\Resource;

use App\Http\Integrations\GoCardless\Requests\Agreements\CreateEua;
use App\Http\Integrations\GoCardless\Resource;
use Saloon\Http\Response;

final class Agreements extends Resource
{
    public function createEua(
        string $institutionId,
        int $maxHistoricalDays = 90,
        int $accessValidForDays = 90,
    ): Response {
        return $this->connector->send(new CreateEua($institutionId, $maxHistoricalDays, $accessValidForDays));
    }
}

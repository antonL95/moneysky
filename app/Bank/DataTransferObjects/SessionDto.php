<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Exceptions\InvalidApiException;

use function Safe\json_encode;

final readonly class SessionDto
{
    /**
     * @param array<string, string> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['link'], $data['requisition_id'], $data['agreement_id'])) {
            throw InvalidApiException::invalidDataEntry(json_encode($data));
        }

        return new self(
            link: $data['link'],
            requisition_id: $data['requisition_id'],
            agreement_id: $data['agreement_id'],
        );
    }

    public function __construct(
        public string $link,
        public string $requisition_id,
        public string $agreement_id,
    ) {
    }
}

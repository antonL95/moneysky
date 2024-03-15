<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Enums\Status;

readonly class UserRequisitionDto
{
    /**
     * @param  array<string, string>  $data
     */
    public static function fromArray(array $data): self
    {
        $status = Status::CREATED;

        try {
            $status = Status::getByShortCode($data['status']);
        } catch (\InvalidArgumentException) {
            try {
                $status = Status::from($data['status']);
            } catch (\ValueError) {
            }
        }

        return new self(
            $data['id'],
            $status,
            $data['link'],
        );
    }

    public function __construct(
        public string $id,
        public Status $status,
        public string $link,
    ) {
    }
}

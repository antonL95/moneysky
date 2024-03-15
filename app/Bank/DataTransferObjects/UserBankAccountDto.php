<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\Bank\Enums\Status;
use App\Bank\Exceptions\InvalidApiException;
use Ramsey\Uuid\Uuid;

readonly class UserBankAccountDto
{
    /**
     * @param array<string, string|int|string[]|null> $data
     *
     * @throws InvalidApiException
     */
    public static function fromArray(array $data): self
    {
        $accType = $data['cashAccountType'] ?? 'checking';

        if (isset($data['status']) && \is_string($data['status'])) {
            try {
                $status = Status::from($data['status']);
            } catch (\ValueError) {
                try {
                    $status = Status::getByShortCode($data['status']);
                } catch (\InvalidArgumentException) {
                    $status = Status::LINKED;
                }
            }
        } else {
            $status = Status::LINKED;
        }

        $id = $data['id'] ?? Uuid::uuid4()->toString();
        $currency = $data['currency'];

        if (!\is_string($id) || !\is_string($currency) || !\is_string($accType)) {
            throw InvalidApiException::invalidDataEntry();
        }
        $name = $data['name'];

        if ($name === null) {
            $name = null;
        } elseif (!\is_string($name)) {
            throw InvalidApiException::invalidDataEntry();
        }

        return new self(
            $id,
            $status,
            $name,
            $currency,
            $accType,
        );
    }

    public function __construct(
        public string $id,
        public Status $status,
        public ?string $name,
        public string $currency,
        public string $type,
    ) {
    }
}

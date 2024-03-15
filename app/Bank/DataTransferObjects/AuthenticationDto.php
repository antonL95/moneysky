<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

readonly class AuthenticationDto
{
    /**
     * @param array<string, string|int|null> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) $data['access'],
            (int) $data['access_expires'],
            $data['refresh'] === null ? null : (string) $data['refresh'],
            $data['refresh_expires'] === null ? null : (int) $data['refresh_expires'],
        );
    }

    public function __construct(
        public string $access,
        public int $accessExpires,
        public ?string $refresh = null,
        public ?int $refreshExpires = null,
    ) {
    }
}

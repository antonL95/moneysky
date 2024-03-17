<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\OpenAi\Exceptions\OpenAiExceptions;

final readonly class TaggedTransactionDto
{
    /**
     * @param array<int|string> $data
     *
     * @throws OpenAiExceptions
     */
    public static function fromArray(array $data): self
    {
        $tag = $data['tag'];
        $id = $data['id'];

        if (!\is_int($id) || !\is_string($tag)) {
            throw OpenAiExceptions::invalidData();
        }

        return new self(
            id: $id,
            tag: $tag,
        );
    }

    public function __construct(
        public int $id,
        public string $tag,
    ) {
    }
}

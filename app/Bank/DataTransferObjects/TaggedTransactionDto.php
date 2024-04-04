<?php

declare(strict_types=1);

namespace App\Bank\DataTransferObjects;

use App\OpenAi\Exceptions\OpenAiExceptions;

use function Safe\json_encode;

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
        $id = (int) $data['id'];

        if (!\is_string($tag)) {
            throw OpenAiExceptions::invalidData(json_encode($data));
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

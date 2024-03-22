<?php

declare(strict_types=1);

namespace App\OpenAi\Services;

use App\Bank\DataTransferObjects\TaggedTransactionDto;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankTransactionRaw;
use App\OpenAi\Exceptions\OpenAiExceptions;
use OpenAI\Laravel\Facades\OpenAI;
use Safe\Exceptions\JsonException;

use function Safe\json_decode;
use function Safe\json_encode;

class OpenAiService
{
    protected const string MODEL = 'gpt-3.5-turbo';

    /**
     * @throws OpenAiExceptions
     */
    public function classifyTransactions(UserBankTransactionRaw $transactionRaw): TaggedTransactionDto
    {
        $tags = TransactionTag::all();

        /** @var array<int, string> $inputTags */
        $inputTags = $tags->map(fn (TransactionTag $tag) => $tag->tag)->toArray();

        $transactionInput = [
            'id' => $transactionRaw->id,
            'text' => $transactionRaw->remittance_information ?? $transactionRaw->additional_information,
            'amount' => round($transactionRaw->balance_cents / 100, 2),
            'currency' => $transactionRaw->currency,
        ];

        $systemPrompt = sprintf(
            'Sort data from this format "%s" using these tags: [%s] and output in this format "%s"',
            '{"id": "123", "text": "description", "amount": 123.45, "currency": "USD"}',
            implode(',', $inputTags),
            '{"id": "123", "tag": "category"}',
        );

        $userPrompt = json_encode($transactionInput);

        $response = OpenAI::chat()->create([
            'model' => self::MODEL,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
        ]);

        if ($response->choices[0]->message->content === null) {
            throw OpenAiExceptions::invalidResponse();
        }

        try {
            /** @var array<string, int|string> $taggedTransaction */
            $taggedTransaction = (array) json_decode($response->choices[0]->message->content, true);
        } catch (JsonException) {
            throw OpenAiExceptions::invalidResponse();
        }

        return TaggedTransactionDto::fromArray($taggedTransaction);
    }
}

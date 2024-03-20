<?php

declare(strict_types=1);

namespace App\OpenAi\Services;

use App\Bank\DataTransferObjects\TaggedTransactionDto;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankTransactionRaw;
use App\OpenAi\Exceptions\OpenAiExceptions;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI;
use Safe\Exceptions\JsonException;

use function Safe\json_decode;
use function Safe\json_encode;

class OpenAiService
{
    protected const string MODEL = 'gpt-3.5-turbo';

    /**
     * @return array<int, TaggedTransactionDto>
     */
    public function classifyTransactions(UserBankTransactionRaw $transactions): array
    {
        $tags = TransactionTag::all();

        /** @var array<int, string> $inputTags */
        $inputTags = $tags->map(fn (TransactionTag $tag) => $tag->tag)->toArray();

        $transactionInput = [
            'id' => $transactions->id,
            'text' => $transactions->remittance_information ?? $transactions->additional_information,
            'amount' => round($transactions->balance_cents / 100, 2),
            'currency' => $transactions->currency,
        ];

        $systemPrompt = sprintf(
            'Sort data from this format "%s" using tags: [%s] and output in this format "%s"',
            '[{"id": "123", "text": "description", "amount": 123.45, "currency": "USD"}]',
            implode(',', $inputTags),
            '[{"id": "123", "tag": "category"}]',
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
            return [];
        }

        try {
            $taggedTransactions = (array) json_decode($response->choices[0]->message->content, true);
        } catch (JsonException) {
            return [];
        }

        $temp = [];
        foreach ($taggedTransactions as $taggedTransaction) {
            try {
                /** @var array<int, string> $taggedTransaction */
                $temp[] = TaggedTransactionDto::fromArray($taggedTransaction);
            } catch (OpenAiExceptions) {
                continue;
            }
        }

        return $temp;
    }
}

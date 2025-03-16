<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\App\Services\TaggedTransactionData;
use App\Models\TransactionTag;
use App\Models\UserBankTransactionRaw;
use EchoLabs\Prism\Enums\Provider;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Schema\ObjectSchema;
use EchoLabs\Prism\Schema\StringSchema;
use JsonException;

final readonly class AiService
{
    /**
     * @throws JsonException
     */
    public function classifyTransactions(UserBankTransactionRaw $transactionRaw): TaggedTransactionData
    {
        $tags = TransactionTag::all();

        /** @var array<int, string> $inputTags */
        $inputTags = $tags->map(
            fn (TransactionTag $tag) => $tag->tag,
        )->toArray();

        $transactionInput = [
            'id' => $transactionRaw->id,
            'text' => $transactionRaw->remittance_information ?? $transactionRaw->additional_information,
            'amount' => round($transactionRaw->balance_cents / 100, 2),
            'currency' => $transactionRaw->currency,
        ];

        $systemPrompt = sprintf(
            'Sort data from this format "%s" using these tags: [%s]',
            '{"id": "123", "text": "description", "amount": 123.45, "currency": "USD"}',
            implode(',', $inputTags),
        );

        $schema = new ObjectSchema(
            name: 'transaction_tag',
            description: 'A structured transaction tag',
            properties: [
                new StringSchema('id', 'Given transaction ID'),
                new StringSchema('tag', 'Tag from the list of tags'),
            ],
            requiredFields: ['id', 'tag']
        );

        $userPrompt = json_encode($transactionInput, JSON_THROW_ON_ERROR);

        $taggedTransaction = Prism::structured()
            ->using(Provider::OpenAI, 'gpt-4o-mini')
            ->withSchema($schema)
            ->withSystemPrompt($systemPrompt)
            ->withPrompt($userPrompt)
            ->generate();

        return TaggedTransactionData::from($taggedTransaction->structured);
    }
}

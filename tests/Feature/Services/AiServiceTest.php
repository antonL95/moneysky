<?php

declare(strict_types=1);

use App\Models\UserBankTransactionRaw;
use App\Services\AiService;
use EchoLabs\Prism\Enums\FinishReason;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Structured\Response as StructuredResponse;
use EchoLabs\Prism\ValueObjects\Meta;
use EchoLabs\Prism\ValueObjects\Usage;

it('can generate structured response', function () {

    $userBankTransactionRaw = UserBankTransactionRaw::factory()->create();

    $fakeResponse = new StructuredResponse(
        steps: collect([]),
        responseMessages: collect([]),
        text: json_encode([
            'id' => (string) $userBankTransactionRaw->id,
            'tag' => 'Groceries',
        ]),
        structured: [
            'id' => (string) $userBankTransactionRaw->id,
            'tag' => 'Groceries',
        ],
        finishReason: FinishReason::Stop,
        usage: new Usage(10, 20),
        meta: new Meta('fake-1', 'fake-model'),
        additionalContent: []
    );

    Prism::fake([$fakeResponse]);

    $transactionTag = app(AiService::class)->classifyTransactions($userBankTransactionRaw);

    expect($transactionTag->id)
        ->toEqual($userBankTransactionRaw->id)
        ->and($transactionTag->tag)
        ->toEqual('Groceries');
});

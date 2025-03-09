<?php

declare(strict_types=1);

use App\Actions\CalculateBudget;
use Carbon\CarbonImmutable;
use Database\Factories\TransactionTagFactory;
use Database\Factories\UserBankAccountFactory;
use Database\Factories\UserBankTransactionRawFactory;
use Database\Factories\UserBudgetFactory;
use Database\Factories\UserFactory;
use Database\Factories\UserTransactionFactory;

it('calculates budget with correct tags', function () {
    $user = UserFactory::new()->create();
    $now = CarbonImmutable::now();

    $tags = [
        1 => 'Groceries',
        2 => 'Dining Out',
    ];

    foreach ($tags as $key => $tag) {
        TransactionTagFactory::new(['tag' => $tag, 'id' => $key])->create();
    }

    $userBudget = UserBudgetFactory::new([
        'user_id' => $user->id,
        'balance_cents' => 100_00,
        'currency' => 'EUR',
    ])->create();

    $userBudget->tags()->sync([1]);

    $userBudgetPeriod = $userBudget->periods()->create([
        'start_date' => $now->startOfMonth(),
        'end_date' => $now->endOfMonth(),
        'balance_cents' => 0,
    ]);

    $bankAccount = UserBankAccountFactory::new()->create();

    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => 1,
        'user_id' => $user->id,
        'balance_cents' => 20_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();
    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => 2,
        'user_id' => $user->id,
        'balance_cents' => 100_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();
    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => 1,
        'user_id' => $user->id,
        'balance_cents' => 15_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();

    $calculateBudget = app(CalculateBudget::class);

    $calculateBudget->handle($userBudgetPeriod);

    expect($userBudgetPeriod->fresh()->balance_cents)->toBe(35_00);
});

it('calculates budget without tags', function () {
    $user = UserFactory::new()->create();
    $now = CarbonImmutable::now();

    $tags = [
        1 => 'Groceries',
        2 => 'Dining Out',
    ];

    foreach ($tags as $key => $tag) {
        TransactionTagFactory::new(['tag' => $tag, 'id' => $key])->create();
    }

    $userBudget = UserBudgetFactory::new([
        'user_id' => $user->id,
        'balance_cents' => 100_00,
        'currency' => 'EUR',
    ])->create();

    $userBudgetPeriod = $userBudget->periods()->create([
        'start_date' => $now->startOfMonth(),
        'end_date' => $now->endOfMonth(),
        'balance_cents' => 0,
    ]);

    $bankAccount = UserBankAccountFactory::new()->create();

    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => 2,
        'user_id' => $user->id,
        'balance_cents' => 20_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();
    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => 1,
        'user_id' => $user->id,
        'balance_cents' => 100_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();
    $raw = UserBankTransactionRawFactory::new([
        'user_bank_account_id' => $bankAccount->id,
    ])->create();

    UserTransactionFactory::new([
        'transaction_tag_id' => null,
        'user_id' => $user->id,
        'balance_cents' => 15_00,
        'currency' => 'EUR',
        'user_bank_account_id' => $bankAccount->id,
        'user_bank_transaction_raw_id' => $raw->id,
    ])->create();

    $calculateBudget = app(CalculateBudget::class);

    $calculateBudget->handle($userBudgetPeriod);

    expect($userBudgetPeriod->fresh()->balance_cents)->toBe(135_00);
});

<?php

declare(strict_types=1);

use App\Actions\TransactionAggregate\CreateTransactionAggregation;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankTransactionRaw;
use App\Models\UserTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Subscription;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

it('calculates the aggregated transactions', function () {
    config(['services.stripe.plus_plan_id' => 'plus']);

    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'plus',
    ]);

    actingAs($user);

    $userBank = UserBankAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    $tags = [
        'Groceries',
        'Dining Out',
        'Utilities',
        'Rent/Mortgage',
        'Streaming Services',
        'Online Subscriptions',
        'Health & Wellness',
        'Shopping',
        'Transportation',
        'Savings & Investments',
    ];

    $calculateTransactionAggregation = $this->app->make(CreateTransactionAggregation::class);

    foreach ($tags as $tag) {
        $tagDb = TransactionTag::factory()->create([
            'tag' => $tag,
            'color' => fake()->hexColor(),
        ]);

        $balance_amount = random_int(100, 1000) * -100;

        $rawTransaction = UserBankTransactionRaw::factory()->create([
            'user_bank_account_id' => $userBank->id,
            'currency' => $userBank->currency,
            'balance_cents' => $balance_amount,
            'external_id' => fake()->uuid,
            'booked_at' => now(),
            'processed' => true,
        ]);

        UserTransaction::factory()->create([
            'user_id' => $user->id,
            'user_bank_transaction_raw_id' => $rawTransaction->id,
            'booked_at' => $rawTransaction->booked_at,
            'balance_cents' => $rawTransaction->balance_cents,
            'currency' => $rawTransaction->currency,
            'user_bank_account_id' => $rawTransaction->user_bank_account_id,
            'transaction_tag_id' => $tagDb->id,
            'description' => $tagDb->tag,
        ]);
    }

    $calculateTransactionAggregation->handle($user);

    assertDatabaseCount(
        'user_transaction_aggregates',
        11,
    );

    foreach ($tags as $tag) {
        $tagDb = TransactionTag::whereTag($tag)->first();
        $balance_amount = random_int(100, 1000) * -100;

        $rawTransaction = UserBankTransactionRaw::factory()->create([
            'user_bank_account_id' => $userBank->id,
            'currency' => $userBank->currency,
            'balance_cents' => $balance_amount,
            'external_id' => fake()->uuid,
            'booked_at' => now(),
            'processed' => true,
        ]);

        UserTransaction::factory()->create([
            'user_id' => $user->id,
            'user_bank_transaction_raw_id' => $rawTransaction->id,
            'booked_at' => $rawTransaction->booked_at,
            'balance_cents' => $rawTransaction->balance_cents,
            'currency' => $rawTransaction->currency,
            'user_bank_account_id' => $rawTransaction->user_bank_account_id,
            'transaction_tag_id' => $tagDb->id,
            'description' => $tagDb->tag,
        ]);
    }

    $calculateTransactionAggregation->handle($user);

    assertDatabaseCount(
        'user_transaction_aggregates',
        11,
    );
});

it('calculates historical aggregates from transactions', function () {
    config(['services.stripe.plus_plan_id' => 'plus']);

    $user = User::factory()->create([
        'demo' => false,
    ]);

    Subscription::factory()->create([
        'user_id' => $user->id,
        'stripe_price' => 'plus',
    ]);

    actingAs($user);

    $userBank = UserBankAccount::factory()->create([
        'user_id' => $user->id,
    ]);

    $tags = [
        'Groceries',
        'Dining Out',
        'Utilities',
        'Rent/Mortgage',
        'Streaming Services',
        'Online Subscriptions',
        'Health & Wellness',
        'Shopping',
        'Transportation',
        'Savings & Investments',
    ];

    foreach ($tags as $tag) {
        TransactionTag::factory()->create([
            'tag' => $tag,
            'color' => fake()->hexColor(),
        ]);
    }

    $calculateTransactionAggregation = $this->app->make(CreateTransactionAggregation::class);

    for ($i = 0; $i < 7; $i++) {
        foreach ($tags as $tag) {
            $tagDb = TransactionTag::whereTag($tag)->firstOrFail();
            $balance_amount = random_int(100, 1000) * -100;

            $rawTransaction = UserBankTransactionRaw::factory()->create([
                'user_bank_account_id' => $userBank->id,
                'currency' => $userBank->currency,
                'balance_cents' => $balance_amount,
                'external_id' => fake()->uuid,
                'booked_at' => now()->subDays($i),
                'processed' => true,
            ]);

            UserTransaction::factory()->create([
                'user_id' => $user->id,
                'user_bank_transaction_raw_id' => $rawTransaction->id,
                'booked_at' => $rawTransaction->booked_at,
                'balance_cents' => $rawTransaction->balance_cents,
                'currency' => $rawTransaction->currency,
                'user_bank_account_id' => $rawTransaction->user_bank_account_id,
                'transaction_tag_id' => $tagDb->id,
                'description' => $tagDb->tag,
            ]);
        }
    }

    $calculateTransactionAggregation->handle($user, CarbonImmutable::now()->subDays(7));

    expect(
        $user->transactionsAggregate()->whereRaw('DATE(aggregate_date) = ?', now()->subDays(2)->setTime(0, 0)->toDateString())->count()
    )->toBe(11);

    $count = DB::selectOne('SELECT COUNT(*) AS `count` FROM (SELECT COUNT(*) FROM `user_transaction_aggregates` WHERE `user_id` = '.$user->id.' GROUP BY `aggregate_date`) AS aggregates')->count;

    expect($count)->toBe(8);
});

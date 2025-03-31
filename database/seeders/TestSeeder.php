<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AssetType;
use App\Helpers\CurrencyHelper;
use App\Models\BankInstitution;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Models\UserBudget;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use App\Models\UserTransaction;
use App\Models\UserTransactionAggregate;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class TestSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(
            static function (): void {
                $user = User::create([
                    'name' => 'Anton Loginov',
                    'email' => 'anton@moneysky.app',
                    'email_verified_at' => now(),
                    'password' => Hash::make('DemoAppPassword'),
                    'demo' => true,
                ]);

                DB::insert(
                    'INSERT INTO "subscriptions" (user_id, type, stripe_id, stripe_status, stripe_price, quantity, trial_ends_at, ends_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [
                        $user->id,
                        'default',
                        Str::uuid()->toString(),
                        'active',
                        config('services.stripe.monthly_price_id'),
                        1,
                        null,
                        now()->addYears(2),
                    ],
                );

                BankInstitution::create([
                    'name' => fake()->company,
                    'bic' => fake()->swiftBicNumber,
                    'external_id' => fake()->uuid,
                    'countries' => ['CZ'],
                    'logo_url' => fake()->imageUrl,
                    'transaction_total_days' => 90,
                    'active' => true,
                ]);

                $bankSession = UserBankSession::create([
                    'user_id' => $user->id,
                    'bank_institution_id' => BankInstitution::inRandomOrder()->first()->id,
                    'agreement_id' => fake()->uuid,
                    'requisition_id' => fake()->uuid,
                    'link' => fake()->url,
                ]);

                $userBankAccount = UserBankAccount::create([
                    'user_id' => $user->id,
                    'name' => 'Checking account',
                    'user_bank_session_id' => $bankSession->id,
                    'iban' => fake()->iban,
                    'external_id' => fake()->uuid,
                    'access_expires_at' => now()->addYears(2),
                    'currency' => 'CZK',
                    'resource_id' => fake()->uuid,
                    'balance_cents' => random_int(50000, 100000) * 100,
                ]);

                foreach ([
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
                ] as $tag) {
                    TransactionTag::create([
                        'tag' => $tag,
                        'color' => fake()->hexColor,
                    ]);
                }

                $now = CarbonImmutable::now();
                $transactions = [];
                $portfolioAssets = [];
                $transactionAggregates = [];

                for ($i = 0; $i < 100; $i++) {
                    $date = $now->subDays($i);
                    $day = $date->toDateString();

                    $snapshot = UserPortfolioSnapshot::create([
                        'aggregate_date' => $day,
                        'balance_cents' => 0,
                        'change' => random_int(-10_00, 10_00) / 100,
                        'user_id' => $user->id,
                    ]);

                    $sum = 0;

                    foreach (AssetType::cases() as $assetType) {
                        $balance = random_int(10000_00, 40000_00);
                        $sum += $balance;

                        $portfolioAssets[] = [
                            'snapshot_id' => $snapshot->id,
                            'asset_type' => $assetType,
                            'user_id' => $user->id,
                            'balance_cents' => $balance,
                            'change' => random_int(-1000, 1000) / 100,
                        ];
                    }

                    $snapshot->balance_cents = $sum;
                    $snapshot->save();

                    $transactionAggregates[] = [
                        'user_id' => $user->id,
                        'aggregate_date' => $day,
                        'transaction_tag_id' => null,
                        'balance_cents' => random_int(10_00, 100_00),
                        'change' => 0,
                    ];

                    TransactionTag::all()->map(function (TransactionTag $tag) use ($day, $user, $userBankAccount, &$transactions, &$transactionAggregates, $date): void {
                        $transactionAggregates[] = [
                            'user_id' => $user->id,
                            'aggregate_date' => $day,
                            'transaction_tag_id' => $tag->id,
                            'balance_cents' => random_int(10_00, 15_00),
                            'change' => 0,
                        ];

                        for ($i = 0; $i < 2; $i++) {
                            $transactions[] = [
                                'user_bank_account_id' => $userBankAccount->id,
                                'user_id' => $user->id,
                                'transaction_tag_id' => $tag->id,
                                'booked_at' => $date->toDateTimeString(),
                                'currency' => $user->currency ?? CurrencyHelper::defaultCurrency(),
                                'description' => fake()->word,
                                'balance_cents' => random_int(10_00, 100_00),
                            ];
                        }
                    });

                    for ($j = 0; $j < 2; $j++) {
                        $transactions[] = [
                            'user_bank_account_id' => $userBankAccount->id,
                            'user_id' => $user->id,
                            'transaction_tag_id' => null,
                            'booked_at' => $date->toDateTimeString(),
                            'currency' => $user->currency ?? CurrencyHelper::defaultCurrency(),
                            'description' => fake()->word,
                            'balance_cents' => random_int(10_00, 100_00),
                        ];
                    }
                }

                UserPortfolioAsset::withoutGlobalScopes()->upsert($portfolioAssets, ['id'], ['balance_cents']);
                UserTransactionAggregate::withoutGlobalScopes()->upsert($transactionAggregates, ['id'], ['balance_cents']);
                UserTransaction::withoutGlobalScopes()->upsert($transactions, ['id'], ['balance_cents']);

                $housingBudgetAmount = random_int(800, 1500) * 100;
                $streamingBudgetAmount = random_int(80, 120) * 100;
                $foodBudgetAmount = random_int(200, 300) * 100;

                $housingBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Housing',
                    'balance_cents' => 2000_00,
                    'currency' => 'EUR',
                ]);

                $housingBudget->tags()->sync(
                    TransactionTag::whereIn('tag', ['Rent/Mortgage', 'Utilities'])->get()->pluck('id')->toArray(),
                );

                $subscriptionBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Subscriptions',
                    'balance_cents' => 150_00,
                    'currency' => 'EUR',
                ]);

                $subscriptionBudget->tags()->sync(
                    TransactionTag::whereIn('tag', [
                        'Streaming Services',
                        'Online Subscriptions',
                    ])->get()->pluck('id')->toArray(),
                );

                $foodBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Food',
                    'balance_cents' => 350_00,
                    'currency' => 'EUR',
                ]);

                $foodBudget->tags()->sync(
                    TransactionTag::whereIn('tag', [
                        'Groceries',
                        'Dining Out',
                    ])->get()->pluck('id')->toArray(),
                );

                for ($i = 0; $i < 2; $i++) {
                    $now = CarbonImmutable::now()->firstOfMonth()->subMonths($i);

                    $housingBudget->periods()->create([
                        'start_date' => $now->startOfMonth()->toDateString(),
                        'end_date' => $now->endOfMonth()->toDateString(),
                        'balance_cents' => $housingBudgetAmount,
                    ]);

                    $subscriptionBudget->periods()->create([
                        'start_date' => $now->startOfMonth()->toDateString(),
                        'end_date' => $now->endOfMonth()->toDateString(),
                        'balance_cents' => $streamingBudgetAmount,
                    ]);

                    $foodBudget->periods()->create([
                        'start_date' => $now->startOfMonth()->toDateString(),
                        'end_date' => $now->endOfMonth()->toDateString(),
                        'balance_cents' => $foodBudgetAmount,
                    ]);
                }
            },
        );
    }
}

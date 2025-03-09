<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AssetType;
use App\Enums\ChainType;
use App\Models\BankInstitution;
use App\Models\Post;
use App\Models\TransactionTag;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Models\UserBankTransactionRaw;
use App\Models\UserBudget;
use App\Models\UserCryptoWallet;
use App\Models\UserKrakenAccount;
use App\Models\UserManualEntry;
use App\Models\UserPortfolioAsset;
use App\Models\UserPortfolioSnapshot;
use App\Models\UserStockMarket;
use App\Models\UserTransactionAggregate;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function in_array;

final class DemoAppSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(
            static function (): void {
                Artisan::call('app:kraken-assets');
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

                UserCryptoWallet::create([
                    'user_id' => $user->id,
                    'chain_type' => ChainType::ETH,
                    'wallet_address' => '0x3dFA77f04314e94716e6F3454594E6750955548B',
                    'balance_cents' => random_int(1000, 10000) * 100,
                    'tokens' => [],
                ]);

                UserKrakenAccount::create([
                    'user_id' => $user->id,
                    'api_key' => fake()->uuid,
                    'private_key' => fake()->uuid,
                    'balance_cents' => random_int(1000, 10000) * 100,
                ]);

                UserManualEntry::create([
                    'user_id' => $user->id,
                    'name' => 'Cash wallet #1',
                    'currency' => 'CZK',
                    'description' => 'My cash wallet',
                    'balance_cents' => random_int(10000, 30000) * 100,
                ]);

                foreach (['AAPL', 'GOOG', 'AMZN', 'NVDA', 'MSFT', 'VOO'] as $ticker) {
                    UserStockMarket::create([
                        'user_id' => $user->id,
                        'ticker' => $ticker,
                        'amount' => random_int(1, 10),
                        'balance_cents' => random_int(100, 1000) * 100,
                    ]);
                }

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
                $housingBudget = random_int(800, 1500) * 100;
                $streamingBudget = random_int(80, 120) * 100;
                $foodBudget = random_int(200, 300) * 100;
                $beginningOfTheMonth = $now->startOfMonth();

                $dayDiff = $now->diffInDays($beginningOfTheMonth);

                for ($i = 0; $i < 30; $i++) {
                    $day = $now->subDays($i)->toDateString();

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
                        UserPortfolioAsset::create([
                            'snapshot_id' => $snapshot->id,
                            'asset_type' => $assetType,
                            'user_id' => $user->id,
                            'balance_cents' => $balance,
                            'change' => random_int(-1000, 1000) / 100,
                        ]);
                    }

                    $snapshot->balance_cents = $sum;
                    $snapshot->save();

                    UserTransactionAggregate::create([
                        'user_id' => $user->id,
                        'aggregate_date' => $day,
                        'transaction_tag_id' => null,
                        'balance_cents' => random_int(10_00, 100_00),
                        'change' => 0,
                    ]);

                    TransactionTag::all()->map(function (TransactionTag $tag) use ($day, $user, $housingBudget, $streamingBudget, $foodBudget, $dayDiff): void {
                        $balance = null;

                        if (in_array($tag->tag, [
                            'Rent/Mortgage',
                            'Utilities',
                        ], true)) {
                            $balance = ($housingBudget / 100) * ($tag->tag === 'Rent/Mortgage' ? 80 : 20);
                        }

                        if (in_array($tag->tag, [
                            'Streaming Services',
                            'Online Subscriptions',
                        ], true)) {
                            $balance = ($streamingBudget / 100) * ($tag->tag === 'Online Subscriptions' ? 60 : 40);
                        }

                        if (in_array($tag->tag, [
                            'Groceries',
                            'Dining Out',
                        ], true)) {
                            $balance = ($foodBudget / 100) * ($tag->tag === 'Dining Out' ? 35 : 65);
                        }

                        UserTransactionAggregate::create([
                            'user_id' => $user->id,
                            'aggregate_date' => $day,
                            'transaction_tag_id' => $tag->id,
                            'balance_cents' => $balance === null ? random_int(10_00, 15_00) : (int) round($balance / $dayDiff, mode: PHP_ROUND_HALF_DOWN),
                            'change' => 0,
                        ]);
                    });

                    UserBankTransactionRaw::factory(random_int(1, 10))->create([
                        'user_bank_account_id' => $userBankAccount->id,
                    ]);
                }

                $userBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Housing',
                    'balance_cents' => 2000_00,
                    'currency' => 'EUR',
                ]);

                $userBudget->tags()->sync(
                    TransactionTag::whereIn('tag', ['Rent/Mortgage', 'Utilities'])->get()->pluck('id')->toArray(),
                );

                $userBudget->periods()->create([
                    'start_date' => CarbonImmutable::now()->startOfMonth()->toDateString(),
                    'end_date' => CarbonImmutable::now()->endOfMonth()->toDateString(),
                    'balance_cents' => $housingBudget,
                ]);

                $userBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Subscriptions',
                    'balance_cents' => 150_00,
                    'currency' => 'EUR',
                ]);

                $userBudget->tags()->sync(
                    TransactionTag::whereIn('tag', [
                        'Streaming Services',
                        'Online Subscriptions',
                    ])->get()->pluck('id')->toArray(),
                );

                $userBudget->periods()->create([
                    'start_date' => CarbonImmutable::now()->startOfMonth()->toDateString(),
                    'end_date' => CarbonImmutable::now()->endOfMonth()->toDateString(),
                    'balance_cents' => $streamingBudget,
                ]);

                $userBudget = UserBudget::create([
                    'user_id' => $user->id,
                    'name' => 'Food',
                    'balance_cents' => 350_00,
                    'currency' => 'EUR',
                ]);

                $userBudget->tags()->sync(
                    TransactionTag::whereIn('tag', [
                        'Groceries',
                        'Dining Out',
                    ])->get()->pluck('id')->toArray(),
                );

                $userBudget->periods()->create([
                    'start_date' => CarbonImmutable::now()->startOfMonth()->toDateString(),
                    'end_date' => CarbonImmutable::now()->endOfMonth()->toDateString(),
                    'balance_cents' => $foodBudget,
                ]);

                $title = fake()->sentence;
                Post::create([
                    'title' => $title,
                    'image_url' => fake()->imageUrl,
                    'slug' => Str::slug($title),
                    'content' => fake()->realText(400),
                    'published_at' => $now,
                ]);
            },
        );
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Bank\Models\BankInstitution;
use App\Bank\Models\TransactionTag;
use App\Bank\Models\UserBankAccount;
use App\Bank\Models\UserBankSession;
use App\Bank\Models\UserBankTransactionRaw;
use App\Bank\Models\UserTransaction;
use App\Crypto\Enums\ChainType;
use App\Crypto\Models\UserCryptoWallets;
use App\Crypto\Models\UserKrakenAccount;
use App\ManualEntry\Models\UserManualEntry;
use App\MarketData\Models\UserStockMarket;
use App\Models\User;
use App\UserSetting\Enums\UserSettingKeys;
use App\UserSetting\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => fake()->name('female'),
            'email' => fake()->email(),
            'email_verified_at' => now(),
            'password' => Hash::make('DemoAppPassword'),
            'demo' => true,
        ]);

        DB::insert('INSERT INTO `subscriptions` (`user_id`, `type`, `stripe_id`, `stripe_status`, `stripe_price`, `quantity`, `trial_ends_at`, `ends_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $user->id,
                'default',
                uniqid('stripe_price_id', true),
                'active',
                config('services.stripe.unlimited_plan_id'),
                1,
                null,
                now()->addYears(2),
            ]);

        $bankSession = UserBankSession::create([
            'user_id' => $user->id,
            'bank_institution_id' => BankInstitution::inRandomOrder()->first()->id,
            'agreement_id' => fake()->uuid,
            'requisition_id' => fake()->uuid,
            'link' => fake()->url,
        ]);

        $userBank = UserBankAccount::create([
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

        UserCryptoWallets::create([
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
            'amount_cents' => random_int(10000, 30000) * 100,
        ]);

        UserSetting::create([
            'user_id' => $user->id,
            'key' => UserSettingKeys::CURRENCY->value,
            'value' => 'CZK',
        ]);

        foreach (['AAPL', 'GOOG', 'AMZN', 'NVDA', 'MSFT'] as $ticker) {
            UserStockMarket::create([
                'user_id' => $user->id,
                'ticker' => $ticker,
                'amount' => random_int(1, 10),
                'price_cents' => random_int(100, 1000) * 100,
            ]);
        }

        $dayDiff = random_int(250, 365);

        for ($i = 0; $i < $dayDiff; ++$i) {
            $nOfTransactions = random_int(1, 3);
            for ($j = 0; $j < $nOfTransactions; ++$j) {
                if ($i > 0 && $i % 30 === 0 && $j === 0) {
                    $randomTag = TransactionTag::where('tag', 'Rent/Mortgage')->first();
                    $balance_amount = random_int(15000, 22000) * -100;
                } else {
                    $randomTag = TransactionTag::where('tag', '!=', 'Rent/Mortgage')->inRandomOrder()->first();
                    $balance_amount = random_int(100, 1000) * -100;
                }

                $rawTransaction = UserBankTransactionRaw::create([
                    'user_bank_account_id' => $userBank->id,
                    'currency' => $userBank->currency,
                    'balance_cents' => $balance_amount,
                    'external_id' => fake()->uuid,
                    'booked_at' => now()->subDays($i),
                    'processed' => true,
                ]);

                UserTransaction::create([
                    'user_id' => $user->id,
                    'user_bank_transaction_raw_id' => $rawTransaction->id,
                    'booked_at' => $rawTransaction->booked_at,
                    'balance_cents' => $rawTransaction->balance_cents,
                    'currency' => $rawTransaction->currency,
                    'user_bank_account_id' => $rawTransaction->user_bank_account_id,
                    'transaction_tag_id' => $randomTag->id,
                    'description' => $randomTag->tag,
                ]);
            }
        }
    }
}

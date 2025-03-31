<?php

declare(strict_types=1);

use App\Enums\BankAccountStatus;
use App\Http\Integrations\GoCardless\GoCardlessConnector;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountBalances;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountMetadata;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountTransactions;
use App\Http\Integrations\GoCardless\Requests\Token\ObtainNewAccessRefreshTokenPair;
use App\Jobs\ProcessBankAccountsJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Models\UserBankTransactionRaw;
use App\Services\BankService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Fake the queue to track dispatched jobs
    Queue::fake();

    // Create a user
    $this->user = User::factory()->create();

    // Create a bank institution
    $this->bankInstitution = BankInstitution::factory()->create();

    // Create a bank session for the user
    $this->bankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Create a bank account for the user
    $this->bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $this->bankSession->id,
        'status' => BankAccountStatus::READY,
        'access_expires_at' => CarbonImmutable::now()->addDays(30),
    ]);

    // Set up time range for transactions
    $this->from = CarbonImmutable::now()->subDays(30);
    $this->to = CarbonImmutable::now();

    // Fake Saloon responses with token response
    $this->client = Saloon::fake([
        ObtainNewAccessRefreshTokenPair::class => MockResponse::make(
            body: '{"access": "test-access-token", "access_expires": 123, "refresh": "test-refresh-token", "refresh_expires": 123}',
        ),
    ]);

    // Create the bank service with mocked client
    $this->bankService = new BankService((new GoCardlessConnector)->withMockClient($this->client));
});

it('marks account as expired when access has expired', function () {
    // Set the bank account to be expired
    $this->bankAccount->access_expires_at = CarbonImmutable::now()->subDays(1);
    $this->bankAccount->save();

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Refresh the bank account from the database
    $this->bankAccount->refresh();

    // Assert the bank account status is now EXPIRED
    expect($this->bankAccount->status)->toBe(BankAccountStatus::EXPIRED);

    // Assert no jobs were dispatched
    Queue::assertNothingPushed();
});

it('returns early when account status check throws exception', function () {
    // Add response that will cause an exception
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(
            body: [
                'id' => 'uniqueId',
                'status' => BankAccountStatus::ERROR->value,
                'institution_id' => $this->bankInstitution->external_id,
                'owner_name' => 'Test Owner',
            ],
            status: 400
        ),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Assert no jobs were dispatched
    Queue::assertNothingPushed();
});

it('fetches balance and updates the bank account', function () {
    // Mock balance response
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => $this->bankInstitution->external_id,
            'owner_name' => 'Test Owner',
        ]),
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 1000,
                    ],
                    'balanceType' => 'closingAvailable',
                ],
            ],
        ]),
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [],
            ],
        ]),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Refresh the bank account from the database
    $this->bankAccount->refresh();

    // Assert the balance was updated
    expect($this->bankAccount->balance_cents)->toBe(1000_00);

    // Assert ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class, function ($job) {
        // We can't access private properties directly, so we'll use reflection
        $reflection = new ReflectionClass($job);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $user = $property->getValue($job);

        return $user->id === $this->user->id;
    });
});

it('handles no data found exception when fetching balance', function () {
    // Mock responses that will cause a NO_DATA_FOUND exception
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => $this->bankInstitution->external_id,
            'owner_name' => 'Test Owner',
        ]),
        RetrieveAccountBalances::class => MockResponse::make(
            body: ['error' => 'No data found'],
            status: 404
        ),
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [],
            ],
        ]),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Assert ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class, function ($job) {
        // We can't access private properties directly, so we'll use reflection
        $reflection = new ReflectionClass($job);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $user = $property->getValue($job);

        return $user->id === $this->user->id;
    });
});

it('handles other exceptions when fetching balance', function () {
    // Mock responses that will cause a generic exception
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => $this->bankInstitution->external_id,
            'owner_name' => 'Test Owner',
        ]),
        RetrieveAccountBalances::class => MockResponse::make(
            body: ['error' => 'Generic error'],
            status: 500
        ),
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [],
            ],
        ]),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Assert ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class, function ($job) {
        // We can't access private properties directly, so we'll use reflection
        $reflection = new ReflectionClass($job);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $user = $property->getValue($job);

        return $user->id === $this->user->id;
    });
});

it('fetches transactions and stores them in the database', function () {
    // Mock balance and transactions responses
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => $this->bankInstitution->external_id,
            'owner_name' => 'Test Owner',
        ]),
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 1000,
                    ],
                    'balanceType' => 'closingAvailable',
                ],
            ],
        ]),
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [
                    [
                        'entryReference' => 'transaction-1',
                        'internalTransactionId' => 'transaction-1-internal',
                        'transactionAmount' => [
                            'amount' => 100,
                            'currency' => 'EUR',
                        ],
                        'bookingDate' => '2025-03-20',
                        'valueDate' => '2025-03-20T12:00:00Z',
                        'remittanceInformationUnstructured' => 'Payment for groceries',
                        'additionalInformation' => 'Additional info',
                        'merchantCategoryCode' => '5411',
                    ],
                    [
                        'entryReference' => null,
                        'internalTransactionId' => 'transaction-2',
                        'transactionAmount' => [
                            'amount' => 200,
                            'currency' => 'EUR',
                        ],
                        'bookingDate' => '2025-03-21',
                        'valueDate' => '2025-03-21T12:00:00Z',
                        'remittanceInformationUnstructured' => 'Restaurant bill',
                        'additionalInformation' => null,
                        'currencyExchange' => [
                            'sourceCurrency' => 'USD',
                            'targetCurrency' => 'EUR',
                            'rate' => '0.85',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Assert transactions were stored in the database
    $transactions = UserBankTransactionRaw::where('user_bank_account_id', $this->bankAccount->id)->get();

    expect($transactions)->toHaveCount(2)
        ->and($transactions[0]->external_id)->toBe('transaction-1')
        ->and($transactions[0]->balance_cents)->toBe(100_00)
        ->and($transactions[0]->currency)->toBe('EUR')
        ->and($transactions[0]->remittance_information)->toBe('Payment for groceries')
        ->and($transactions[0]->additional_information)->toBe('Additional info')
        ->and($transactions[0]->merchant_category_code)->toBe('5411')
        ->and($transactions[1]->external_id)->toBe('transaction-2')
        ->and($transactions[1]->balance_cents)->toBe(200_00)
        ->and($transactions[1]->currency)->toBe('EUR')
        ->and($transactions[1]->remittance_information)->toBe('Restaurant bill')
        ->and($transactions[1]->additional_information)->toBeNull();

    // Assert ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('handles empty transactions response', function () {
    // Mock balance response with empty transactions
    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => $this->bankInstitution->external_id,
            'owner_name' => 'Test Owner',
        ]),
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 1000,
                    ],
                    'balanceType' => 'closingAvailable',
                ],
            ],
        ]),
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [],
            ],
        ]),
    ]);

    // Create the job
    $job = new ProcessBankAccountsJob(
        $this->bankAccount,
        $this->from,
        $this->to
    );

    // Run the job
    $job->handle($this->bankService);

    // Assert no transactions were stored
    $transactions = UserBankTransactionRaw::where('user_bank_account_id', $this->bankAccount->id)->get();
    expect($transactions)->toHaveCount(0);

    // Assert ProcessSnapshotJob was dispatched with the correct user
    Queue::assertPushed(ProcessSnapshotJob::class, function ($job) {
        // We can't access private properties directly, so we'll use reflection
        $reflection = new ReflectionClass($job);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $user = $property->getValue($job);

        return $user->id === $this->user->id;
    });
});

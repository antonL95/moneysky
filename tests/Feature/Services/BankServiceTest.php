<?php

declare(strict_types=1);

use App\Data\App\BankAccount\BankInstitutionData;
use App\Enums\BankAccountStatus;
use App\Exceptions\InvalidApiExceptionAbstract;
use App\Http\Integrations\GoCardless\GoCardlessConnector;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountBalances;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountDetails;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountMetadata;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountTransactions;
use App\Http\Integrations\GoCardless\Requests\Agreements\CreateEua;
use App\Http\Integrations\GoCardless\Requests\Institutions\RetrieveAllSupportedInstitutionsInGivenCountry;
use App\Http\Integrations\GoCardless\Requests\Requisitions\CreateRequisition;
use App\Http\Integrations\GoCardless\Requests\Requisitions\DeleteRequisitionById;
use App\Http\Integrations\GoCardless\Requests\Requisitions\RequisitionById;
use App\Http\Integrations\GoCardless\Requests\Token\ObtainNewAccessRefreshTokenPair;
use App\Jobs\ProcessBankAccountsJob;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Services\BankService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    $this->client = Saloon::fake([
        ObtainNewAccessRefreshTokenPair::class => MockResponse::make(
            body: '{"access": "asdfasdf", "access_expires": 123, "refresh": "asdfasdfa", "refresh_expires": 123}',
        ),
    ]);

    $this->bankService = new BankService((new GoCardlessConnector)->withMockClient($this->client));
});

it('return institutions', function () {
    // setup
    $this->client->addResponses([
        RetrieveAllSupportedInstitutionsInGivenCountry::class => MockResponse::make(
            body: '[{"id": "DIREKT_HELADEF1822","name": "1822direkt","bic": "HELADEF1822","transaction_total_days": "730","countries": ["DE"],"logo": "https://cdn-logos.gocardless.com/ais/DIREKT_HELADEF1822.png"},
{"id": "365_NBSBSKBX","name": "365 Bank","bic": "NBSBSKBX","transaction_total_days": "90","countries": ["SK"],"logo": "https://cdn-logos.gocardless.com/ais/365_NBSBSKBX.png"}]',
        ),
    ]);
    // action
    $data = $this->bankService->getInstitutions();
    // assert
    expect($data->count())->toBe(2);
});

it('creates a bank account', function () {
    $queue = Queue::fake();
    $bankInstitution = BankInstitution::factory()->create();
    $user = User::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['uniqueId']]),
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => 'READY',
            'institution_id' => $bankInstitution->external_id,
        ]),
        RetrieveAccountDetails::class => MockResponse::make(
            body: [
                'account' => ['currency' => 'CZK'],
            ],
        ),
    ]);
    actingAs($user);

    assertDatabaseCount(UserBankAccount::class, 0);

    $this->bankService->create($user, $userBankSession->requisition_id);

    $queue->assertPushed(ProcessBankAccountsJob::class);

    assertDatabaseCount(UserBankAccount::class, 1);

    $bankAccount = UserBankAccount::first();

    expect($bankAccount->external_id)->toBe('uniqueId');
});

it('reconnects a bank account', function () {
    $queue = Queue::fake();
    $bankInstitution = BankInstitution::factory()->create();
    $user = User::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $user->id,
    ]);

    $now = CarbonImmutable::now();

    $bankAccount = UserBankAccount::factory()->create([
        'user_bank_session_id' => $userBankSession->id,
        'user_id' => $user->id,
        'external_id' => $bankInstitution->external_id,
        'access_expires_at' => $now->subDays(30),
    ]);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['uniqueId']]),
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => $bankInstitution->external_id,
            'status' => 'READY',
            'institution_id' => $bankInstitution->external_id,
        ]),
        RetrieveAccountDetails::class => MockResponse::make(
            body: [
                'account' => ['currency' => 'CZK'],
            ],
        ),
    ]);

    actingAs($user);

    assertDatabaseCount(UserBankAccount::class, 1);

    expect($bankAccount->access_expires_at->format('Y-m-d'))->toBe($now->subDays(30)->format('Y-m-d'));

    $this->bankService->create($user, $userBankSession->requisition_id, $userBankSession);

    $queue->assertPushed(ProcessBankAccountsJob::class);

    assertDatabaseCount(UserBankAccount::class, 1);

    $bankAccount = $bankAccount->fresh();

    expect($bankAccount->access_expires_at->format('Y-m-d'))->toBe($now->addDays(90)->format('Y-m-d'));
});

it('retrieves account balance', function () {
    $user = User::factory()->create();
    actingAs($user);

    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $this->client->addResponses([
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 100,
                    ],
                    'balanceType' => 'closingAvailable',
                ],
                [
                    'balanceAmount' => [
                        'amount' => 100,
                    ],
                    'balanceType' => 'somethingElse',
                ],
            ],
        ]),
    ]);

    $balance = $this->bankService->getAccountBalance($bankAccount);

    expect($balance->balance)->toBe(100_00);
});

it('throws exception account balance', function () {
    $user = User::factory()->create();
    actingAs($user);

    $this->client->addResponses([
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'asdfasdf' => [],
        ]),
    ]);

    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    expect(
        fn () => $this->bankService->getAccountBalance($bankAccount)
    )->toThrow(InvalidApiExceptionAbstract::class);
});

it('skips invalid balance value', function () {
    $user = User::factory()->create();
    actingAs($user);

    $this->client->addResponses([
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 123,
                    ],
                    'balanceType' => 'closingAvailable',
                ],
                'asdfasdf',
            ],
        ]),
    ]);

    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $balance = $this->bankService->getAccountBalance($bankAccount);

    expect($balance->balance)->toBe(123_00);
});

it('chooses first balance from array', function () {
    $user = User::factory()->create();
    actingAs($user);

    $this->client->addResponses([
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 200,
                    ],
                    'balanceType' => 'asdfasdf',
                ],
                'asdfasdf',
            ],
        ]),
    ]);

    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $balance = $this->bankService->getAccountBalance($bankAccount);

    expect($balance->balance)->toBe(200_00);
});

it('retrieves account transactions', function () {
    $user = User::factory()->create();
    actingAs($user);

    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $this->client->addResponses([
        RetrieveAccountTransactions::class => MockResponse::make(body: [
            'transactions' => [
                'booked' => [
                    [
                        'internalTransactionId' => 'uniqueTransactionId',
                        'transactionAmount' => [
                            'amount' => 100,
                            'currency' => 'CZK',
                        ],
                    ],
                    [
                        'internalTransactionId' => 'uniqueTransactionId2',
                        'transactionAmount' => [
                            'amount' => 102,
                            'currency' => 'CZK',
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $transactions = $this->bankService->getAccountTransactions(
        $bankAccount,
        CarbonImmutable::now(),
        CarbonImmutable::now(),
    );

    expect($transactions->count())
        ->toBe(2)
        ->and($transactions->first()->balance)
        ->toBe(100_00);
});

it('deletes user requisitions', function () {
    $user = User::factory()->create();
    UserBankSession::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->client->addResponses([
        DeleteRequisitionById::class => MockResponse::make(body: ''),
    ]);

    assertDatabaseCount(UserBankSession::class, 1);
    $this->bankService->deleteUserRequisitions($user);
    assertDatabaseCount(UserBankSession::class, 0);
});

it('throws if account is not ready', function () {
    $user = User::factory()->create();
    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::ERROR->value,
            'institution_id' => 'uniqueId',
        ]),
    ]);

    $this->bankService->isAccountStatusReady($bankAccount);
})->throws(InvalidApiExceptionAbstract::class);

it('does nothing if status does not change', function () {
    $user = User::factory()->create();
    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id]);

    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => 'uniqueId',
        ]),
    ]);

    expect($bankAccount->status->value)->toBe(BankAccountStatus::READY->value);

    $this->bankService->isAccountStatusReady($bankAccount);

    expect($bankAccount->status->value)->toBe(BankAccountStatus::READY->value);
});

it('successfully saves status', function () {
    $user = User::factory()->create();
    $bankAccount = UserBankAccount::factory()->create(['user_id' => $user->id, 'status' => BankAccountStatus::EXPIRED->value]);

    $this->client->addResponses([
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'status' => BankAccountStatus::READY->value,
            'institution_id' => 'uniqueId',
        ]),
    ]);

    expect($bankAccount->status->value)->toBe(BankAccountStatus::EXPIRED->value);

    $this->bankService->isAccountStatusReady($bankAccount);

    expect($bankAccount->status->value)->toBe(BankAccountStatus::READY->value);
});

it('returns a proper link', function () {
    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: ['id' => 'uniqueId', 'link' => route('bank-account.callback')]),
    ]);

    assertDatabaseCount(UserBankSession::class, 0);

    $bankInstitution = BankInstitution::factory()->create();
    $user = User::factory()->create();

    $link = $this->bankService->connect($bankInstitution, $user);

    actingAs($user);

    assertDatabaseCount(UserBankSession::class, 1);

    $session = UserBankSession::first();

    expect($session->link)->toBe($link);
});

it('returns a reconnect link', function () {
    $user = User::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: ['id' => 'uniqueId', 'link' => route('bank-account.renew-callback', ['userBankSession' => $userBankSession->id])]),
    ]);

    actingAs($user);

    assertDatabaseCount(UserBankSession::class, 1);

    $session = $user->userBankSession()->latest('id')->first();

    expect($session->link)->toBe($userBankSession->link);

    $bankInstitution = BankInstitution::factory()->create();

    $link = $this->bankService->connect($bankInstitution, $user, $userBankSession);

    assertDatabaseCount(UserBankSession::class, 2);

    $session = $user->userBankSession()->latest('id')->first();

    expect($session->link)->toBe($link);
});

it('returns active bank institutions', function () {
    BankInstitution::factory(10)->create();

    BankInstitution::factory()->create([
        'active' => false,
    ]);

    $service = app(BankService::class);

    $institutions = $service->getActiveBankInstitutions();

    expect(count($institutions))->toBe(4);
});

it('search active bank institutions returns empty collection when no matches', function () {
    // Arrange
    BankInstitution::factory()->create([
        'name' => 'Test Bank',
    ]);

    // Act
    $result = $this->bankService->searchActiveBankInstitutions('NonExistentBank');

    // Assert
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

it('search active bank institutions returns matching institutions', function () {
    // Arrange
    $bank1 = BankInstitution::factory()->create([
        'name' => 'Test Bank One',
        'countries' => ['US', 'CA'],
    ]);

    $bank2 = BankInstitution::factory()->create([
        'name' => 'Test Bank Two',
        'countries' => ['GB', 'FR'],
    ]);

    BankInstitution::factory()->create([
        'name' => 'Different Bank',
    ]);

    // Act
    $result = $this->bankService->searchActiveBankInstitutions('Test Bank');

    // Assert
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(BankInstitutionData::class);

    $resultArray = $result->toArray();

    expect($resultArray[0])
        ->id->toBe($bank1->id)
        ->name->toBe($bank1->name)
        ->logo->toBe($bank1->logo_url)
        ->countries->toBe('US, CA')
        ->and($resultArray[1])
        ->id->toBe($bank2->id)
        ->name->toBe($bank2->name)
        ->logo->toBe($bank2->logo_url)
        ->countries->toBe('GB, FR');
});

it('search active bank institutions limits results to 25', function () {
    // Arrange
    BankInstitution::factory()->count(30)->create([
        'name' => fn (array $attributes) => 'Test Bank',
    ]);

    // Act
    $result = $this->bankService->searchActiveBankInstitutions('Test Bank');

    // Assert
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(25);
});

it('search active bank institutions handles null countries', function () {
    // Arrange
    $bank = BankInstitution::factory()->create([
        'name' => 'Test Bank',
        'countries' => null,
    ]);

    // Act
    $result = $this->bankService->searchActiveBankInstitutions('Test Bank');

    // Assert
    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->each->toBeInstanceOf(BankInstitutionData::class);

    $resultArray = $result->toArray();

    expect($resultArray[0])
        ->id->toBe($bank->id)
        ->name->toBe($bank->name)
        ->logo->toBe($bank->logo_url)
        ->countries->toBeNull();
});

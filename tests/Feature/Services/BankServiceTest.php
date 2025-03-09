<?php

declare(strict_types=1);

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
use Illuminate\Support\Facades\Queue;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertSoftDeleted;

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

it('returns a proper link', function () {
    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: ['id' => 'uniqueId', 'link' => 'test.com/link']),
    ]);

    assertDatabaseCount(UserBankSession::class, 0);

    $bankInstitution = BankInstitution::factory()->create();
    $user = User::factory()->create();

    $this->bankService->connect($bankInstitution, $user);

    actingAs($user);

    assertDatabaseCount(UserBankSession::class, 1);

    $session = UserBankSession::first();

    expect($session->link)->toBe('test.com/link');
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
    assertSoftDeleted(UserBankSession::class);
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

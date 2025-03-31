<?php

declare(strict_types=1);

use App\Enums\BankAccountStatus;
use App\Enums\FlashMessageType;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountBalances;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountDetails;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountMetadata;
use App\Http\Integrations\GoCardless\Requests\Accounts\RetrieveAccountTransactions;
use App\Http\Integrations\GoCardless\Requests\Agreements\CreateEua;
use App\Http\Integrations\GoCardless\Requests\Requisitions\CreateRequisition;
use App\Http\Integrations\GoCardless\Requests\Requisitions\RequisitionById;
use App\Http\Integrations\GoCardless\Requests\Token\ObtainNewAccessRefreshTokenPair;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;
use Laravel\Cashier\Subscription;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'demo' => false,
    ]);

    $this->bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Subscription::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $this->client = Saloon::fake([
        ObtainNewAccessRefreshTokenPair::class => MockResponse::make(
            body: '{"access": "asdfasdf", "access_expires": 123, "refresh": "asdfasdfa", "refresh_expires": 123}',
        ),
    ]);
});

it('shows bank accounts list for subscribed user', function () {
    actingAs($this->user);

    $response = get(route('bank-account.index'));

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('bank-account/index')
        ->has('columns', 4)
        ->has('rows', 1)
        ->has('rows.0', fn (AssertableInertia $row) => $row
            ->where('id', $this->bankAccount->id)
            ->where('name', $this->bankAccount->name)
            ->where('balance', $this->bankAccount->balance)
            ->where('accessExpired', false)
            ->where('status', BankAccountStatus::READY)
            ->etc(),
        ),
    );
});

it('redirects to login when not authenticated', function () {
    $response = get(route('bank-account.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

it('redirects to subscription page when user cannot add more resources', function () {
    $user = User::factory()->create([
        'demo' => true,
    ]);

    actingAs($user);

    $response = get(route('bank-account.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('subscribe'));
});

it('updates bank account name', function () {
    actingAs($this->user);

    $response = put(route('bank-account.update', $this->bankAccount), [
        'name' => 'New Account Name',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Bank Account update successful',
    ]);

    $this->bankAccount->refresh();
    expect($this->bankAccount->name)->toBe('New Account Name');
});

it('prevents updating bank account of another user', function () {
    $otherUser = User::factory()->create();
    $otherBankAccount = UserBankAccount::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    actingAs($this->user);

    $response = put(route('bank-account.update', $otherBankAccount), [
        'name' => 'New Account Name',
    ]);

    $response->assertStatus(404);

    $otherBankAccount->refresh();
    expect($otherBankAccount->name)->not->toBe('New Account Name');
});

it('deletes bank account', function () {
    actingAs($this->user);

    $response = delete(route('bank-account.destroy', $this->bankAccount));

    $response->assertStatus(302);
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Bank Account deletion successful',
    ]);

    expect(UserBankAccount::count())->toBe(0);
});

it('prevents deleting bank account of another user', function () {
    $otherUser = User::factory()->create();
    $otherBankAccount = UserBankAccount::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    actingAs($this->user);

    $response = delete(route('bank-account.destroy', $otherBankAccount));

    $response->assertStatus(404);

    expect(UserBankAccount::withoutGlobalScopes()->count())->toBe(2);
});

it('redirects to bank institution connection', function () {
    $bankInstitution = BankInstitution::factory()->create();

    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: ['id' => 'uniqueId', 'link' => route('bank-account.callback')]),
    ]);

    actingAs($this->user);

    $response = get(route('bank-account.redirect', $bankInstitution));

    $response->assertStatus(302);
    $response->assertRedirect();
});

it('handles successful bank account connection', function () {
    actingAs($this->user);
    $bankInstitution = BankInstitution::factory()->create();
    UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'requisition_id' => 'test-ref',
        'bank_institution_id' => $bankInstitution->id,
    ]);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['test-ref']]),
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'test-ref',
            'status' => 'READY',
            'institution_id' => $bankInstitution->external_id,
        ]),
        RetrieveAccountDetails::class => MockResponse::make(
            body: [
                'account' => ['currency' => 'CZK'],
            ],
        ),
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

    $response = get(route('bank-account.callback', [
        'ref' => 'test-ref',
    ]));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.index'));
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Bank Account creation successful',
    ]);
});

it('handles failed bank account connection', function () {
    actingAs($this->user);
    $bankInstitution = BankInstitution::factory()->create();
    UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'requisition_id' => 'test-ref',
        'bank_institution_id' => $bankInstitution->id,
    ]);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['test-ref']]),
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'test-ref',
            'status' => 'READY',
            'institution_id' => $bankInstitution->external_id,
        ]),
        RetrieveAccountDetails::class => MockResponse::make(
            body: [
                'account' => ['currency' => 'CZK'],
            ],
        ),
    ]);

    $response = get(route('bank-account.callback', [
        'ref' => 'non-existent-ref',
    ]));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.index'));
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::DANGER->value,
        'title' => 'Bank Account creation unsuccessful',
    ]);
});

it('redirects to bank account renewal', function () {
    $bankInstitution = BankInstitution::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'bank_institution_id' => $bankInstitution->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $userBankSession->id,
    ]);

    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: [
            'id' => 'uniqueId',
            'link' => route('bank-account.renew-callback', ['userBankSession' => $userBankSession->id]),
        ]),
    ]);

    actingAs($this->user);

    $response = get(route('bank-account.renew-redirect', $bankAccount));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.renew-callback', ['userBankSession' => $userBankSession->id]));
});

it('handles successful bank account renewal', function () {
    $bankInstitution = BankInstitution::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'bank_institution_id' => $bankInstitution->id,
        'requisition_id' => 'test-ref',
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $userBankSession->id,
        'external_id' => 'test-ref',
    ]);

    actingAs($this->user);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['test-ref']]),
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

    $response = get(route('bank-account.renew-callback', [
        'userBankSession' => $userBankSession->id,
        'ref' => 'test-ref',
    ]));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.index'));
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Bank Account renewal successful',
    ]);
});

it('handles failed bank account renewal', function () {
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $userBankSession->id,
    ]);

    actingAs($this->user);

    $response = get(route('bank-account.renew-callback', [
        'userBankSession' => $userBankSession->id,
        'ref' => 'invalid-ref',
    ]));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.index'));
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::DANGER->value,
        'title' => 'Bank Account renewal unsuccessful',
    ]);
});

it('handles bank account renewal', function () {
    actingAs($this->user);
    $bankInstitution = BankInstitution::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'requisition_id' => 'test-ref',
        'bank_institution_id' => $bankInstitution->id,
    ]);

    $this->client->addResponses([
        RequisitionById::class => MockResponse::make(body: ['accounts' => ['test-ref']]),
        RetrieveAccountMetadata::class => MockResponse::make(body: [
            'id' => 'test-ref',
            'status' => 'READY',
            'institution_id' => $bankInstitution->external_id,
        ]),
        RetrieveAccountDetails::class => MockResponse::make(
            body: [
                'account' => ['currency' => 'CZK'],
            ],
        ),
        RetrieveAccountBalances::class => MockResponse::make(body: [
            'balances' => [
                [
                    'balanceAmount' => [
                        'amount' => 100,
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

    $response = get(route('bank-account.renew-callback', [
        'userBankSession' => $userBankSession,
        'ref' => 'test-ref',
    ]));

    $response->assertStatus(302);
    $response->assertRedirect(route('bank-account.index'));
    $response->assertSessionHas('flash', [
        'type' => FlashMessageType::SUCCESS->value,
        'title' => 'Bank Account renewal successful',
    ]);
});

it('handles bank account renewal redirect', function () {
    actingAs($this->user);
    $bankInstitution = BankInstitution::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
        'bank_institution_id' => $bankInstitution->id,
    ]);
    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $userBankSession->id,
    ]);

    $this->client->addResponses([
        CreateEua::class => MockResponse::make(body: ['id' => 'uniqueId']),
        CreateRequisition::class => MockResponse::make(body: ['id' => 'uniqueId', 'link' => route('bank-account.renew-callback', ['userBankSession' => $userBankSession->id])]),
    ]);

    $response = get(route('bank-account.renew-redirect', ['userBankAccount' => $bankAccount]));

    $response->assertStatus(302);
    $response->assertRedirect();
});

it('handles bank account renewal redirect with missing institution', function () {
    actingAs($this->user);
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $this->user->id,
    ]);
    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $this->user->id,
        'user_bank_session_id' => $userBankSession->id,
    ]);

    $response = get(route('bank-account.renew-redirect', ['userBankAccount' => $bankAccount]));

    $response->assertStatus(500);
});

it('handles bank account renewal redirect for unauthorized user', function () {
    $otherUser = User::factory()->create();

    $bankInstitution = BankInstitution::factory()->create();
    $userBankSession = UserBankSession::factory()->create([
        'user_id' => $otherUser->id,
        'bank_institution_id' => $bankInstitution->id,
    ]);

    $bankAccount = UserBankAccount::factory()->create([
        'user_id' => $otherUser->id,
        'user_bank_session_id' => $userBankSession->id,
    ]);

    actingAs($this->user);

    $response = get(
        route('bank-account.renew-redirect', ['userBankAccount' => $bankAccount->id]),
    );

    $response->assertStatus(404); // because bank account is scoped to auth user
});

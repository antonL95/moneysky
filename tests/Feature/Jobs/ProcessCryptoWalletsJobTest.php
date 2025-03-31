<?php

declare(strict_types=1);

use App\Enums\ChainType;
use App\Http\Integrations\Blockchain\BlockchainConnector;
use App\Http\Integrations\Blockchain\Requests\GetTicker;
use App\Http\Integrations\Blockchain\Requests\GetTokenBalancesForAddress as GetBitcoinBalancesForAddress;
use App\Http\Integrations\Moralis\MoralisConnector;
use App\Http\Integrations\Moralis\Requests\GetTokenBalancesForAddress;
use App\Jobs\ProcessCryptoWalletsJob;
use App\Jobs\ProcessSnapshotJob;
use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user
    $this->user = User::factory()->create();

    // Create ETH and BTC wallets
    $this->ethWallet = UserCryptoWallet::factory()->create([
        'user_id' => $this->user->id,
        'chain_type' => ChainType::ETH,
        'wallet_address' => '0x1234567890123456789012345678901234567890',
        'balance_cents' => 0,
        'tokens' => [],
    ]);

    $this->btcWallet = UserCryptoWallet::factory()->create([
        'user_id' => $this->user->id,
        'chain_type' => ChainType::BTC,
        'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'balance_cents' => 0,
        'tokens' => [],
    ]);

    Queue::fake();
});

it('processes ETH wallet and dispatches snapshot job', function () {
    // Mock Moralis API response for ETH wallet
    Saloon::fake([
        GetTokenBalancesForAddress::class => MockResponse::make([
            'result' => [
                [
                    'token_address' => '0x123...',
                    'symbol' => 'ETH',
                    'usd_value' => 2500.50,
                ],
                [
                    'token_address' => '0x456...',
                    'symbol' => 'USDT',
                    'usd_value' => 100.00,
                ],
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->ethWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that the wallet was updated with correct balance
    $this->ethWallet->refresh();
    expect($this->ethWallet->balance_cents)->toBe(260050)
        ->and($this->ethWallet->tokens)->toBe([
            'ETH' => 250050, // 2500.50 * 100
            'USDT' => 10000, // 100.00 * 100
        ]); // (2500.50 + 100.00) * 100

    // Assert that the snapshot job was dispatched
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('processes BTC wallet and dispatches snapshot job', function () {
    // Mock Blockchain.info API responses
    Saloon::fake([
        GetBitcoinBalancesForAddress::class => MockResponse::make([
            [
                'final_balance' => 100000000, // 1 BTC in satoshis
            ],
        ]),
        GetTicker::class => MockResponse::make([
            'USD' => [
                'last' => 45000.00,
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->btcWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that the wallet was updated with correct balance
    $this->btcWallet->refresh();
    expect($this->btcWallet->balance_cents)->toBe(4500000)
        ->and($this->btcWallet->tokens)->toBe([
            'BTC' => 4500000,
        ]); // 1 BTC * $45,000

    // Assert that the snapshot job was dispatched
    Queue::assertPushed(ProcessSnapshotJob::class);
});

it('handles case when user is null', function () {
    // Delete the user
    $this->user->delete();

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->ethWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that no snapshot job was dispatched
    Queue::assertNotPushed(ProcessSnapshotJob::class);
});

it('handles case when response is not a Response instance', function () {
    $fakeWallet = UserCryptoWallet::factory()->create([
        'user_id' => $this->user->id,
        'chain_type' => ChainType::MATIC,
        'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
        'balance_cents' => 0,
        'tokens' => [],
    ]);
    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($fakeWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that no snapshot job was dispatched
    Queue::assertNotPushed(ProcessSnapshotJob::class);
});

it('skips non-array currency items in ETH wallet', function () {
    // Mock Moralis API response with non-array items
    Saloon::fake([
        GetTokenBalancesForAddress::class => MockResponse::make([
            'result' => [
                'not_an_array',
                null,
                123,
                [
                    'token_address' => '0x123...',
                    'symbol' => 'ETH',
                    'usd_value' => 2500.50,
                ],
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->ethWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that only valid items were processed
    $this->ethWallet->refresh();
    expect($this->ethWallet->balance_cents)->toBe(250050)
        ->and($this->ethWallet->tokens)->toBe([
            'ETH' => 250050,
        ]);
});

it('skips ETH currency items without usd_value', function () {
    // Mock Moralis API response with missing usd_value
    Saloon::fake([
        GetTokenBalancesForAddress::class => MockResponse::make([
            'result' => [
                [
                    'token_address' => '0x123...',
                    'symbol' => 'ETH',
                ],
                [
                    'token_address' => '0x456...',
                    'symbol' => 'USDT',
                    'usd_value' => 100.00,
                ],
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->ethWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that only valid items were processed
    $this->ethWallet->refresh();
    expect($this->ethWallet->balance_cents)->toBe(10000)
        ->and($this->ethWallet->tokens)->toBe([
            'USDT' => 10000,
        ]);
});

it('skips ETH currency items with non-numeric usd_value', function () {
    // Mock Moralis API response with non-numeric usd_value
    Saloon::fake([
        GetTokenBalancesForAddress::class => MockResponse::make([
            'result' => [
                [
                    'token_address' => '0x123...',
                    'symbol' => 'ETH',
                    'usd_value' => 'not_numeric',
                ],
                [
                    'token_address' => '0x456...',
                    'symbol' => 'USDT',
                    'usd_value' => 100.00,
                ],
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->ethWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that only valid items were processed
    $this->ethWallet->refresh();
    expect($this->ethWallet->balance_cents)->toBe(10000)
        ->and($this->ethWallet->tokens)->toBe([
            'USDT' => 10000,
        ]);
});

it('skips BTC currency items without final_balance', function () {
    // Mock Blockchain.info API responses with missing final_balance
    Saloon::fake([
        GetBitcoinBalancesForAddress::class => MockResponse::make([
            [
                'some_other_field' => 'value',
            ],
        ]),
        GetTicker::class => MockResponse::make([
            'USD' => [
                'last' => 45000.00,
            ],
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->btcWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that no balance was updated
    $this->btcWallet->refresh();
    expect($this->btcWallet->balance_cents)->toBe(0)
        ->and($this->btcWallet->tokens)->toBe([]);
});

it('skips BTC processing when USD response is not an array', function () {
    // Mock Blockchain.info API responses with invalid USD response
    Saloon::fake([
        GetBitcoinBalancesForAddress::class => MockResponse::make([
            [
                'final_balance' => 100000000,
            ],
        ]),
        GetTicker::class => MockResponse::make([
            'USD' => 'not_an_array',
        ]),
    ]);

    // Create and dispatch the job
    $job = new ProcessCryptoWalletsJob($this->btcWallet);
    $job->handle(
        app(MoralisConnector::class),
        app(BlockchainConnector::class),
    );

    // Assert that no balance was updated
    $this->btcWallet->refresh();
    expect($this->btcWallet->balance_cents)->toBe(0)
        ->and($this->btcWallet->tokens)->toBe([]);
});

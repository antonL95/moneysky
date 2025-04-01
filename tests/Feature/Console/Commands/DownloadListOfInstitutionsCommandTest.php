<?php

declare(strict_types=1);

use App\Console\Commands\DownloadListOfInstitutionsCommand;
use App\Http\Integrations\GoCardless\GoCardlessConnector;
use App\Http\Integrations\GoCardless\Requests\Institutions\RetrieveAllSupportedInstitutionsInGivenCountry;
use App\Models\BankInstitution;
use App\Services\BankService;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->client = Saloon::fake([
        RetrieveAllSupportedInstitutionsInGivenCountry::class => MockResponse::make([
            [
                'id' => 'DIREKT_HELADEF1822',
                'name' => '1822direkt',
                'bic' => 'HELADEF1822',
                'transaction_total_days' => '730',
                'countries' => ['DE'],
                'logo' => 'https://cdn-logos.gocardless.com/ais/DIREKT_HELADEF1822.png',
            ],
            [
                'id' => '365_NBSBSKBX',
                'name' => '365 Bank',
                'bic' => 'NBSBSKBX',
                'transaction_total_days' => '90',
                'countries' => ['SK'],
                'logo' => 'https://cdn-logos.gocardless.com/ais/365_NBSBSKBX.png',
            ],
        ]),
    ]);

    $this->bankService = new BankService((new GoCardlessConnector)->withMockClient($this->client));
});

it('downloads and stores institutions', function () {
    // Create an existing institution that should be marked as inactive
    $existingInstitution = BankInstitution::factory()->create([
        'external_id' => 'EXISTING_BANK',
        'active' => true,
    ]);

    // Run the command
    artisan(DownloadListOfInstitutionsCommand::class)
        ->assertExitCode(0);

    // Assert that existing institution is marked as inactive
    assertDatabaseHas('bank_institutions', [
        'external_id' => 'EXISTING_BANK',
        'active' => false,
    ]);

    // Assert that new institutions are created
    assertDatabaseHas('bank_institutions', [
        'external_id' => 'DIREKT_HELADEF1822',
        'name' => '1822direkt',
        'bic' => 'HELADEF1822',
        'transaction_total_days' => 730,
        'logo_url' => 'https://cdn-logos.gocardless.com/ais/DIREKT_HELADEF1822.png',
        'active' => 1,
    ]);

    assertDatabaseHas('bank_institutions', [
        'external_id' => '365_NBSBSKBX',
        'name' => '365 Bank',
        'bic' => 'NBSBSKBX',
        'transaction_total_days' => 90,
        'logo_url' => 'https://cdn-logos.gocardless.com/ais/365_NBSBSKBX.png',
        'active' => 1,
    ]);

    // Assert that cache is cleared
    expect(Cache::has('bank-institutions'))->toBeFalse();
});

it('updates existing institutions', function () {
    // Create an existing institution that should be updated
    $existingInstitution = BankInstitution::factory()->create([
        'external_id' => 'DIREKT_HELADEF1822',
        'name' => 'Old Name',
        'active' => false,
    ]);

    // Run the command
    artisan(DownloadListOfInstitutionsCommand::class)
        ->assertExitCode(0);

    // Assert that the institution is updated
    assertDatabaseHas('bank_institutions', [
        'external_id' => 'DIREKT_HELADEF1822',
        'name' => '1822direkt',
        'active' => true,
    ]);

    // Assert that only one record exists (no duplicates)
    assertDatabaseCount('bank_institutions', 2);

    assertDatabaseHas('bank_institutions', [
        'external_id' => '365_NBSBSKBX',
        'name' => '365 Bank',
        'bic' => 'NBSBSKBX',
        'transaction_total_days' => 90,
        'logo_url' => 'https://cdn-logos.gocardless.com/ais/365_NBSBSKBX.png',
        'active' => 1,
    ]);
});

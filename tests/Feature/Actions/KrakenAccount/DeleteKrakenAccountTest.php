<?php

use App\Actions\KrakenAccount\DeleteKrakenAccount;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserKrakenAccount;
use function Pest\Laravel\assertDatabaseCount;

it('successfully deletes account', function () {
    Queue::fake();
    $account = UserKrakenAccount::factory()->create();

    assertDatabaseCount('user_kraken_accounts', 1);

    $action = app(DeleteKrakenAccount::class);

    $action->handle($account);
    assertDatabaseCount('user_kraken_accounts', 0);
    Queue::assertPushed(ProcessSnapshotJob::class);
});

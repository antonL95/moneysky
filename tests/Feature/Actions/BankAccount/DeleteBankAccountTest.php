<?php

declare(strict_types=1);

use App\Actions\BankAccount\DeleteBankAccount;
use App\Jobs\ProcessSnapshotJob;
use App\Models\UserBankAccount;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('successfully deletes', function () {
    Queue::fake();
    $bankAccount = UserBankAccount::factory()->create();

    assertDatabaseHas('user_bank_accounts', [
        'id' => $bankAccount->id,
    ]);

    app(DeleteBankAccount::class)->handle($bankAccount);

    assertDatabaseMissing('user_bank_accounts', [
        'id' => $bankAccount->id,
    ]);

    Queue::assertPushed(ProcessSnapshotJob::class);
});

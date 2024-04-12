<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Models\User;
use App\Services\BankService;
use Laravel\Jetstream\Contracts\DeletesUsers;

readonly class DeleteUser implements DeletesUsers
{
    public function __construct(
        protected BankService $bankService,
    ) {
    }

    public function delete(User $user): void
    {
        $this->bankService->deleteUserRequisitions($user);
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}

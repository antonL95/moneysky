<?php

declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Bank\Services\BankService;
use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    public function __construct(
        protected readonly BankService $bankService,
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

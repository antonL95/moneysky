<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserBankAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserBankAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function view(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->id === $userBankAccount->user_id;
    }

    public function create(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function renew(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->canAddAdditionalResource() && $userBankAccount->user_id === $user->id;
    }

    public function update(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->id === $userBankAccount->user_id;
    }

    public function delete(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->id === $userBankAccount->user_id;
    }

    public function restore(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->id === $userBankAccount->user_id;
    }

    public function forceDelete(User $user, UserBankAccount $userBankAccount): bool
    {
        return $user->id === $userBankAccount->user_id;
    }
}

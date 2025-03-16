<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

final class UserTransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }

    public function delete(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }

    public function hide(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }

    public function restore(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }

    public function forceDelete(User $user, UserTransaction $userTransaction): bool
    {
        return $user->id === $userTransaction->user_id;
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserBudget;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserBudgetPolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserBudget $userBudget): bool
    {
        return $user->id === $userBudget->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, UserBudget $userBudget): bool
    {
        return $user->id === $userBudget->user_id;
    }

    public function delete(User $user, UserBudget $userBudget): bool
    {
        return $user->id === $userBudget->user_id;
    }

    public function restore(User $user, UserBudget $userBudget): bool
    {
        return $user->id === $userBudget->user_id;
    }

    public function forceDelete(User $user, UserBudget $userBudget): bool
    {
        return $user->id === $userBudget->user_id;
    }
}

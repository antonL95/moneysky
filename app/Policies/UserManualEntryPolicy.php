<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserManualEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserManualEntryPolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserManualEntry $userManualEntry): bool
    {
        return $user->id === $userManualEntry->user_id;
    }

    public function create(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function update(User $user, UserManualEntry $userManualEntry): bool
    {
        return $user->id === $userManualEntry->user_id;
    }

    public function delete(User $user, UserManualEntry $userManualEntry): bool
    {
        return $user->id === $userManualEntry->user_id;
    }

    public function restore(User $user, UserManualEntry $userManualEntry): bool
    {
        return $user->id === $userManualEntry->user_id;
    }

    public function forceDelete(User $user, UserManualEntry $userManualEntry): bool
    {
        return $user->id === $userManualEntry->user_id;
    }
}

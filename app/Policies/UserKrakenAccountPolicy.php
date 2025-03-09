<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserKrakenAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserKrakenAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function view(User $user, UserKrakenAccount $userKrakenAccount): bool
    {
        return $user->id === $userKrakenAccount->user_id;
    }

    public function create(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function update(User $user, UserKrakenAccount $userKrakenAccount): bool
    {
        return $user->id === $userKrakenAccount->user_id;
    }

    public function delete(User $user, UserKrakenAccount $userKrakenAccount): bool
    {
        return $user->id === $userKrakenAccount->user_id;
    }

    public function restore(User $user, UserKrakenAccount $userKrakenAccount): bool
    {
        return $user->id === $userKrakenAccount->user_id;
    }

    public function forceDelete(User $user, UserKrakenAccount $userKrakenAccount): bool
    {
        return $user->id === $userKrakenAccount->user_id;
    }
}

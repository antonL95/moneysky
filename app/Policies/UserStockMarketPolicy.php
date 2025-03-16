<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserStockMarket;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserStockMarketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function view(User $user, UserStockMarket $userStockMarket): bool
    {
        return $user->id === $userStockMarket->user_id;
    }

    public function create(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function update(User $user, UserStockMarket $userStockMarket): bool
    {
        return $user->id === $userStockMarket->user_id;
    }

    public function delete(User $user, UserStockMarket $userStockMarket): bool
    {
        return $user->id === $userStockMarket->user_id;
    }

    public function restore(User $user, UserStockMarket $userStockMarket): bool
    {
        return $user->id === $userStockMarket->user_id;
    }

    public function forceDelete(User $user, UserStockMarket $userStockMarket): bool
    {
        return $user->id === $userStockMarket->user_id;
    }
}

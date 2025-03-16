<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserCryptoWallet;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class UserCryptoWalletPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function view(User $user, UserCryptoWallet $userCryptoWallet): bool
    {
        return $userCryptoWallet->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->canAddAdditionalResource();
    }

    public function update(User $user, UserCryptoWallet $userCryptoWallet): bool
    {
        return $userCryptoWallet->user_id === $user->id;
    }

    public function delete(User $user, UserCryptoWallet $userCryptoWallet): bool
    {
        return $userCryptoWallet->user_id === $user->id;
    }

    public function restore(User $user, UserCryptoWallet $userCryptoWallet): bool
    {
        return $userCryptoWallet->user_id === $user->id;
    }

    public function forceDelete(User $user, UserCryptoWallet $userCryptoWallet): bool
    {
        return $userCryptoWallet->user_id === $user->id;
    }
}

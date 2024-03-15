<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Exceptions\InvalidScopeException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * @throws InvalidScopeException
     */
    public function apply(Builder $builder, Model $model): void // @phpstan-ignore-line
    {
        $user = auth()->user();

        if ($user === null) {
            throw InvalidScopeException::invalidUserScope();
        }

        $builder->where('user_id', $user->id);
    }
}

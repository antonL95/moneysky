<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Exceptions\InvalidScopeExceptionAbstract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class UserScope implements Scope
{
    /**
     * @throws InvalidScopeExceptionAbstract
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // @codeCoverageIgnoreStart
        if ($user === null) {
            throw InvalidScopeExceptionAbstract::invalidUserScope();
        }
        // @codeCoverageIgnoreEnd

        $builder->where('user_id', $user->id);
    }
}

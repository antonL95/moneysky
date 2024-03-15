<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

test('model are classes')
    ->expect('App\Models')
    ->toBeClasses();

test('globals')
    ->expect(['dd', 'dump'])
    ->not
    ->toBeUsed();

test('Models')
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring('App\Models\Scopes');

test('app')
    ->expect('App')
    ->toUseStrictTypes();

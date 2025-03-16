<?php

declare(strict_types=1);

use App\Http\Controllers\App\SocialController;
use App\Http\Controllers\App\UserBankAccountController;

test('app')
    ->expect('App')
    ->toUseStrictTypes();

arch()->preset()->laravel()->ignoring([
    SocialController::class,
    UserBankAccountController::class,
    'App\Http\Integrations',
]);
arch()->preset()->security();

<?php

declare(strict_types=1);

use App\Http\Controllers\App\SocialController;
use App\Http\Controllers\App\StaticPageController;
use App\Http\Controllers\App\UserBankAccountController;

test('app')
    ->expect('App')
    ->toUseStrictTypes();

arch()->preset()->laravel()->ignoring([
    StaticPageController::class,
    SocialController::class,
    UserBankAccountController::class,
    'App\Http\Integrations',
]);
arch()->preset()->security();

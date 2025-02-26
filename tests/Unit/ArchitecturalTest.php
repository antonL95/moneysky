<?php

declare(strict_types=1);

test('app')
    ->expect('App')
    ->toUseStrictTypes();

arch()->preset()->laravel();
arch()->preset()->security();

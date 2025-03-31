<?php

declare(strict_types=1);

use App\Enums\ChainType;

it('returns correct pretty name for ETH', function () {
    expect(ChainType::ETH->getPrettyName())->toBe('Ethereum');
});

it('returns correct pretty name for MATIC', function () {
    expect(ChainType::MATIC->getPrettyName())->toBe('Polygon (matic)');
});

it('returns correct pretty name for BTC', function () {
    expect(ChainType::BTC->getPrettyName())->toBe('Bitcoin');
});

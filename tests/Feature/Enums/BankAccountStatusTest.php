<?php

declare(strict_types=1);

use App\Enums\BankAccountStatus;

it('returns correct name for READY status', function () {
    expect(BankAccountStatus::READY->getName())->toBe(__('Ready'));
});

it('returns correct name for DISCOVERED status', function () {
    expect(BankAccountStatus::DISCOVERED->getName())->toBe(__('Discovered'));
});

it('returns correct name for ERROR status', function () {
    expect(BankAccountStatus::ERROR->getName())->toBe(__('Error'));
});

it('returns correct name for EXPIRED status', function () {
    expect(BankAccountStatus::EXPIRED->getName())->toBe(__('Expired'));
});

it('returns correct name for PROCESSING status', function () {
    expect(BankAccountStatus::PROCESSING->getName())->toBe(__('Processing'));
});

it('returns correct name for SUSPENDED status', function () {
    expect(BankAccountStatus::SUSPENDED->getName())->toBe(__('Suspended'));
});

it('returns correct badge color for READY status', function () {
    expect(BankAccountStatus::READY->getBadgeColor())->toBe('green');
});

it('returns correct badge color for non-READY statuses', function () {
    expect(BankAccountStatus::DISCOVERED->getBadgeColor())->toBe('red');
    expect(BankAccountStatus::ERROR->getBadgeColor())->toBe('red');
    expect(BankAccountStatus::EXPIRED->getBadgeColor())->toBe('red');
    expect(BankAccountStatus::PROCESSING->getBadgeColor())->toBe('red');
    expect(BankAccountStatus::SUSPENDED->getBadgeColor())->toBe('red');
});

<?php

declare(strict_types=1);

use App\Concerns\HasSubscription;

// Create a test class that uses the HasSubscription trait
final class TestClassWithSubscription
{
    use HasSubscription;

    public function __construct(
        public readonly bool $demo,
    ) {}

    public function subscribed(): bool
    {
        return true;
    }
}

it('returns false for demo users', function () {
    $testClass = new TestClassWithSubscription(demo: true);

    expect($testClass->canAddAdditionalResource())->toBeFalse();
});

it('returns true for subscribed non-demo users', function () {
    $testClass = new TestClassWithSubscription(demo: false);

    expect($testClass->canAddAdditionalResource())->toBeTrue();
});

it('returns false for non-subscribed non-demo users', function () {
    $testClass = new class(demo: false)
    {
        use HasSubscription;

        public function __construct(
            public readonly bool $demo,
        ) {}

        public function subscribed(): bool
        {
            return false;
        }
    };

    expect($testClass->canAddAdditionalResource())->toBeFalse();
});

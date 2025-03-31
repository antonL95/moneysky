<?php

declare(strict_types=1);

use App\Concerns\HasRedirectWithFlashMessage;
use App\Enums\FlashMessageAction;
use App\Enums\FlashMessageType;
use Illuminate\Http\RedirectResponse;

// Create a test controller that uses the HasRedirectWithFlashMessage trait
final class TestControllerWithFlashMessage
{
    use HasRedirectWithFlashMessage;
}

beforeEach(function () {
    $this->controller = new TestControllerWithFlashMessage();
});

it('generates correct flash message for success update', function () {
    $result = $this->controller->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::UPDATE);

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::SUCCESS->value);
});

it('generates correct flash message for success create', function () {
    $result = $this->controller->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::CREATE);

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::SUCCESS->value);
});

it('generates correct flash message for success delete', function () {
    $result = $this->controller->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::DELETE);

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::SUCCESS->value);
});

it('generates correct flash message for success renew', function () {
    $result = $this->controller->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::RENEW);

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::SUCCESS->value);
});

it('generates correct flash message for error update', function () {
    $result = $this->controller->flashMessage(FlashMessageType::DANGER, FlashMessageAction::UPDATE);

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::DANGER->value);
});

it('generates correct subscription message', function () {
    $result = $this->controller->subscriptionMessage();

    expect($result)->toBeArray()
        ->toHaveKey('title')
        ->toHaveKey('description')
        ->toHaveKey('type')
        ->and($result['type'])->toBe(FlashMessageType::DANGER->value)
        ->and($result['description'])->toBe('You need to upgrade your subscription.');
});

it('success method returns back response when no route provided', function () {
    $response = $this->controller->success(FlashMessageAction::UPDATE);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getSession()?->get('flash'))->toBeArray()
        ->and($response->getSession()?->get('flash.type'))->toBe(FlashMessageType::SUCCESS->value);
});

it('success method returns redirect response when route provided', function () {
    $response = $this->controller->success(FlashMessageAction::UPDATE, 'dashboard');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe(url('dashboard'))
        ->and($response->getSession()?->get('flash'))->toBeArray()
        ->and($response->getSession()?->get('flash.type'))->toBe(FlashMessageType::SUCCESS->value);
});

it('error method returns back response when no route provided', function () {
    $response = $this->controller->error(FlashMessageAction::UPDATE);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getSession()?->get('flash'))->toBeArray()
        ->and($response->getSession()?->get('flash.type'))->toBe(FlashMessageType::DANGER->value);
});

it('error method returns redirect response when route provided', function () {
    $response = $this->controller->error(FlashMessageAction::UPDATE, 'dashboard');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe(url('dashboard'))
        ->and($response->getSession()?->get('flash'))->toBeArray()
        ->and($response->getSession()?->get('flash.type'))->toBe(FlashMessageType::DANGER->value);
});

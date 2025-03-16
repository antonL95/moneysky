<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Enums\FlashMessageAction;
use App\Enums\FlashMessageType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasRedirectWithFlashMessage
{
    public function success(FlashMessageAction $action, ?string $route = null): RedirectResponse
    {
        if ($route === null) {
            return back()->with(
                'flash',
                $this->flashMessage(FlashMessageType::SUCCESS, $action),
            );
        }

        return redirect($route)->with(
            'flash',
            $this->flashMessage(FlashMessageType::SUCCESS, $action),
        );
    }

    public function error(FlashMessageAction $action, ?string $route = null): RedirectResponse
    {
        if ($route === null) {
            return back()->with(
                'flash',
                $this->flashMessage(FlashMessageType::DANGER, $action),
            );
        }

        return redirect($route)->with(
            'flash',
            $this->flashMessage(FlashMessageType::DANGER, $action),
        );
    }

    public function errorSubscription(): RedirectResponse
    {
        return redirect()->route('subscribe');
    }

    /**
     * @return array<string,string>
     */
    public function flashMessage(
        FlashMessageType $type,
        FlashMessageAction $action,
    ): array {
        $title = sprintf(
            '%s %s %s',
            $this->resourceName(),
            match ($action) {
                FlashMessageAction::UPDATE => 'update',
                FlashMessageAction::CREATE => 'creation',
                FlashMessageAction::DELETE => 'deletion',
                FlashMessageAction::RENEW => 'renewal',
            },
            match ($type) {
                FlashMessageType::SUCCESS => 'successful',
                FlashMessageType::DANGER => 'unsuccessful',
            },
        );

        return [
            'title' => $title,
            'type' => $type->value,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function subscriptionMessage(): array
    {
        return [
            'title' => sprintf(
                '%s error',
                $this->resourceName(),
            ),
            'description' => 'You need to upgrade your subscription.',
            'type' => FlashMessageType::DANGER->value,
        ];
    }

    private function resourceName(): string
    {
        return implode(
            ' ',
            Str::ucsplit(
                Str::chopStart(
                    Str::chopEnd(
                        type(
                            Arr::last(
                                explode('\\', $this::class),
                            ),
                        )->asString(),
                        'Controller',
                    ),
                    'User',
                ),
            ),
        );
    }
}

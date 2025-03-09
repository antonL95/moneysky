<?php

declare(strict_types=1);

namespace App\Concerns;

trait HasSubscription
{
    public function canAddAdditionalResource(): bool
    {
        if ($this->demo) {
            return false;
        }

        return (bool) $this->subscribed();
    }
}

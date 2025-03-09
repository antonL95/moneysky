<?php

declare(strict_types=1);

namespace App\Http\Integrations\GoCardless;

use Saloon\Http\Connector;

abstract class Resource
{
    public function __construct(
        protected Connector $connector,
    ) {}
}

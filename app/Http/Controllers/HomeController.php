<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BankService;
use Inertia\Inertia;
use Inertia\Response;

final readonly class HomeController
{
    public function __invoke(BankService $bankService): Response
    {
        return Inertia::render('home/index', [
            'banks' => Inertia::defer(static fn (): array => $bankService->getActiveBankInstitutions()),
        ]);
    }
}

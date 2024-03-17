<?php

declare(strict_types=1);

namespace App\Bank\Console;

use App\Bank\Models\BankInstitution;
use App\Bank\Services\BankService;
use Illuminate\Console\Command;

use function Safe\json_encode;

class DownloadListOfInstitutionsCommand extends Command
{
    protected $signature = 'app:download-institutions';

    protected $description = 'Download the list of institutions from the bank API and store it in the database.';

    public function __construct(
        private readonly BankService $bankService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $institutions = $this->bankService->getInstitutions();

        $temp = [];

        foreach ($institutions as $institution) {
            $temp[] = [
                'name' => $institution->name,
                'external_id' => $institution->id,
                'transaction_total_days' => $institution->transactionTotalDays,
                'countries' => json_encode($institution->countries),
                'bic' => $institution->bic,
                'logo_url' => $institution->logoUrl,
            ];
        }

        BankInstitution::insertOrIgnore($temp);
    }
}

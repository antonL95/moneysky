<?php

declare(strict_types=1);

namespace App\Bank\Console;

use App\Bank\Contracts\IBankClient;
use App\Bank\Models\BankInstitution;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use function Safe\json_encode;

class DownloadListOfInstitutionsCommand extends Command
{
    protected $signature = 'app:download-list-of-institutions';

    protected $description = 'Download the list of institutions from the bank API and store it in the database.';

    public function __construct(
        private readonly IBankClient $bankClient,

    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $institutions = $this->bankClient->getInstitutions();

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

        Cache::remember('bank-institutions', 60 * 60 * 24, static fn () => BankInstitution::all());
    }
}

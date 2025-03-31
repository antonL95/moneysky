<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\GoCardless\InstitutionsData;
use App\Models\BankInstitution;
use App\Services\BankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class DownloadListOfInstitutionsCommand extends Command
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

        DB::transaction(static function () use ($institutions): void {
            DB::update('UPDATE "bank_institutions" SET "active" = FALSE');

            $institutions->each(function (InstitutionsData $data): void {
                $institution = BankInstitution::where('external_id', $data->id)->first();

                if ($institution instanceof BankInstitution) {
                    $institution->name = $data->name;
                    $institution->bic = $data->bic;
                    $institution->transaction_total_days = (int) $data->transaction_total_days;
                    $institution->countries = $data->countries;
                    $institution->logo_url = $data->logo;
                    $institution->active = true;
                    $institution->save();
                } else {
                    BankInstitution::create(
                        [
                            'external_id' => $data->id,
                            'name' => $data->name,
                            'bic' => $data->bic,
                            'transaction_total_days' => $data->transaction_total_days,
                            'countries' => $data->countries,
                            'logo_url' => $data->logo,
                            'active' => true,
                        ]
                    );
                }
            });
        });

        Cache::forget('bank-institutions');
    }
}

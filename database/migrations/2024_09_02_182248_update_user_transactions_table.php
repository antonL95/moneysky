<?php

declare(strict_types=1);

use App\Models\UserManualEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_transactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_bank_account_id')
                ->nullable()
                ->change();
            $table->unsignedBigInteger('user_bank_transaction_raw_id')
                ->nullable()
                ->change();
            $table->foreignIdFor(UserManualEntry::class)
                ->nullable()
                ->after('user_bank_transaction_raw_id')
                ->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('user_transactions', function (Blueprint $table): void {
            //
        });
    }
};

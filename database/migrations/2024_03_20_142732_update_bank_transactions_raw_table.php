<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_bank_transactions_raw', function (Blueprint $table): void {
            $table->dropIndex('user_bank_transactions_raw_external_id_index');
            $table->string('external_id', 132)->nullable()->index()->change();
            $table->string('merchant_category_code')->nullable()->after('booked_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_bank_transactions_raw', function (Blueprint $table): void {
            $table->dropIndex('user_bank_transactions_raw_external_id_index');
            $table->string('external_id', 64)->nullable()->index()->change();
            $table->dropColumn('merchant_category_code');
        });
    }
};

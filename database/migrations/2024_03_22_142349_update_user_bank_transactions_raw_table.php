<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_bank_transactions_raw', static function (Blueprint $table): void {
            $table->boolean('processed')->default(false)->after('merchant_category_code')->index();
        });
    }

    public function down(): void
    {
        Schema::table('user_bank_transactions_raw', static function (Blueprint $table): void {
            $table->dropColumn(['processed']);
        });
    }
};

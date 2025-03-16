<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_stock_markets', static function (Blueprint $table): void {
            $table->renameColumn('price_cents', 'balance_cents');
        });

        Schema::table('user_manual_entries', static function (Blueprint $table): void {
            $table->renameColumn('amount_cents', 'balance_cents');
        });
    }

    public function down(): void
    {
        Schema::table('user_stock_markets', static function (Blueprint $table): void {
            $table->renameColumn('balance_cents', 'price_cents');
        });

        Schema::table('user_manual_entries', static function (Blueprint $table): void {
            $table->renameColumn('balance_cents', 'amount_cents');
        });
    }
};

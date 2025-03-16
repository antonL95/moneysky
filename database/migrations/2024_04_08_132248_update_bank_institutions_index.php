<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_institutions', function (Blueprint $table): void {
            $table->index('name');
        });

        Schema::table('user_stock_markets', function (Blueprint $table): void {
            $table->unique(['user_id', 'ticker']);
        });
    }

    public function down(): void
    {
        //
    }
};

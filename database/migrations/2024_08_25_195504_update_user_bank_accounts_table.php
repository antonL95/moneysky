<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_bank_accounts', function (Blueprint $table): void {
            $table->bigInteger('balance_cents')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table): void {
            $table->bigInteger('balance_cents')
                ->unsigned()
                ->default(0)->change();
        });
    }
};

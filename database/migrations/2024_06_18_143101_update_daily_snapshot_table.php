<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_portfolio_snapshots', static function (Blueprint $table): void {
            $table->date('aggregate_date')->after('change');
        });
    }

    public function down(): void
    {
        Schema::table('user_portfolio_snapshots', static function (Blueprint $table): void {
            $table->dropColumn('aggregate_date');
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_portfolio_assets', function (Blueprint $table): void {
            $table->unique(['user_id', 'snapshot_id', 'asset_type'], 'snapshot_asset_unique');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table): void {
            $table->dropUnique('snapshot_asset_unique');
        });
    }
};

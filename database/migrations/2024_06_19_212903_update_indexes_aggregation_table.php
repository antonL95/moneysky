<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_transaction_aggregates', function (Blueprint $table): void {
            $table->unique(['user_id', 'transaction_tag_id', 'aggregate_date'], 'transaction_aggregate_unique_index');
        });
        Schema::table('user_portfolio_snapshots', function (Blueprint $table): void {
            $table->unique(['user_id', 'aggregate_date']);
        });
    }

    public function down(): void
    {
        //
    }
};

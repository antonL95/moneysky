<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_transaction_tag_id');
        });
        Schema::table('user_budget_tags', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_transaction_tag_id');
        });
        Schema::drop('user_transaction_tags');
    }
};

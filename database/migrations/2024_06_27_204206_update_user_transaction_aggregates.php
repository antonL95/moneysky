<?php

declare(strict_types=1);

use App\Models\TransactionTag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_transaction_aggregates', function (Blueprint $table): void {
            $table->foreignIdFor(TransactionTag::class)->change()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('user_transaction_aggregates', function (Blueprint $table): void {
            $table->foreignIdFor(TransactionTag::class)->change();
        });
    }
};

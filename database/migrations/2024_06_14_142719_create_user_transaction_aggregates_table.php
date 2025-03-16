<?php

declare(strict_types=1);

use App\Models\TransactionTag;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_transaction_aggregates', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(TransactionTag::class, 'transaction_tag_id');
            $table->date('aggregate_date');
            $table->bigInteger('balance_cents');
            $table->float('change');
            $table->timestamps();
            $table->index('aggregate_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_transaction_aggregates');
    }
};

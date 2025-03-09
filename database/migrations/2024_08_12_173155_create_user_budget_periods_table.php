<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_budget_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_budget_id')->constrained('user_budgets')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('balance_cents');
            $table->unique(['user_budget_id', 'start_date', 'end_date'], 'unique_user_budget_periods');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_budget_periods');
    }
};

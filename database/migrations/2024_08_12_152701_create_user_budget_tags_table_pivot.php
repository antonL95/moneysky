<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_budget_tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_budget_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_tag_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_transaction_tag_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_budget_tags');
    }
};

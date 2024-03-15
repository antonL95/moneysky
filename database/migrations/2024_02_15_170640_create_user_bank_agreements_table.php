<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_bank_agreements', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bank_institution_id')->constrained('bank_institutions')->cascadeOnDelete();
            $table->uuid('external_id')->unique();
            $table->integer('max_historical_days')->default(90);
            $table->integer('access_valid_for_days')->default(30);
            $table->json('access_scope');
            $table->dateTime('accepted_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_data_agreements');
    }
};

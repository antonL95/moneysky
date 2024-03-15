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
        Schema::create('user_bank_requisitions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_bank_agreement_id')->constrained('user_bank_agreements')->cascadeOnDelete();
            $table->foreignId('bank_institution_id')->constrained('bank_institutions')->cascadeOnDelete();
            $table->uuid('external_id')->unique();
            $table->json('status');
            $table->json('accounts')->nullable();
            $table->string('user_language');
            $table->string('link');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_data_requisitions');
    }
};

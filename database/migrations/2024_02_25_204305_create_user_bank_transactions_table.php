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
        Schema::create('user_bank_transactions_raw', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_bank_account_id');
            $table->string('external_id')->nullable();
            $table->bigInteger('balance_cents');
            $table->string('currency');
            $table->json('currency_exchange')->nullable();
            $table->string('additional_information', 512)->nullable();
            $table->string('remittance_information', 560)->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamps();
            $table->foreign('user_bank_account_id')->references('id')->on('user_bank_accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_transactions_raw');
    }
};

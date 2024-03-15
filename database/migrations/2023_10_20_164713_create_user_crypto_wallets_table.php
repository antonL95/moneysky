<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_crypto_wallets', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('wallet_address', 512);
            $table->string('chain_type', 32);
            $table->unsignedBigInteger('balance_cents')->nullable();
            $table->json('tokens')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'wallet_address', 'chain_type'], 'user_wallet_chain_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_crypto_wallets');
    }
};

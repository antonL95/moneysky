<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraken_trading_pairs', static function (Blueprint $table) {
            $table->id();
            $table->string('key_pair')->unique();
            $table->string('crypto')->index();
            $table->string('fiat')->index();
            $table->unsignedBigInteger('trade_value_cents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraken_trading_pairs');
    }
};

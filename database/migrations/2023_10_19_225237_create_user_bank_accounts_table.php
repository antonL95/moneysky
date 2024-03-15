<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_bank_accounts', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->uuid('external_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('balance_cents')->default(0);
            $table->string('status')->nullable();
            $table->string('currency');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};

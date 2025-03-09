<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_social_providers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('provider_slug');
            $table->string('provider_user_id')->index();
            $table->string('nickname')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('avatar')->nullable();
            $table->text('provider_data')->nullable();
            $table->string('token')->index();
            $table->string('refresh_token')->nullable();
            $table->string('token_expires_at');
            $table->timestamps();
            $table->unique(['user_id', 'provider_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_social_providers');
    }
};

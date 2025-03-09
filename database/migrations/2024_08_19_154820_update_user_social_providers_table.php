<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_social_providers', function (Blueprint $table): void {
            $table->dropIndex('user_social_providers_token_index');
            $table->string('token', 1024)->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_social_providers', function (Blueprint $table): void {
            $table->string('token', 255)->change();
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_bank_accounts', static function (Blueprint $table): void {
            $table->after('access_expires_at', function (Blueprint $table): void {
                $table->string('status')->default('READY');
            });
        });
    }

    public function down(): void
    {
        Schema::table('user_bank_accounts', static function (Blueprint $table): void {
            $table->dropColumn('status');
        });
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_institutions', function (Blueprint $table): void {
            $table->boolean('active')->after('logo_url');
        });
    }

    public function down(): void
    {
        Schema::table('bank_institutions', function (Blueprint $table): void {
            $table->dropColumn('active');
        });
    }
};

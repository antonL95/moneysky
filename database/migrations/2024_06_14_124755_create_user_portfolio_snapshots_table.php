<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_portfolio_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->unsignedBigInteger('balance_cents');
            $table->float('change');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_portfolio_snapshots');
    }
};

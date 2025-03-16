<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraken_trading_pairs', static function (Blueprint $table): void {
            $table->id();
            $table->string('key_pair')->unique();
            $table->string('crypto')->index();
            $table->string('fiat')->index();
            $table->unsignedBigInteger('trade_value_cents');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });

        Schema::create('user_kraken_accounts', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('balance_cents')->nullable();
            $table->string('api_key', 1024);
            $table->string('private_key', 1024);
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('user_crypto_wallets', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('wallet_address', 512);
            $table->string('chain_type', 32);
            $table->unsignedBigInteger('balance_cents')->nullable();
            $table->json('tokens')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'wallet_address', 'chain_type'], 'user_wallet_chain_unique');
        });
        Schema::create('user_stock_markets', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ticker');
            $table->decimal('amount', 20, 6)->nullable();
            $table->unsignedBigInteger('price_cents')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['ticker']);
        });
        Schema::create('bank_institutions', static function (Blueprint $table): void {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('name');
            $table->string('bic');
            $table->integer('transaction_total_days')->default(90);
            $table->json('countries')->nullable();
            $table->string('logo_url');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
        });
        Schema::create('user_manual_entries', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('amount_cents');
            $table->string('currency');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
        Schema::create('newsletter_subscribers', static function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique()->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
        Schema::create('user_settings', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('key')->index();
            $table->string('value');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'key']);
        });
        Schema::create('user_bank_sessions', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bank_institution_id');
            $table->string('link');
            $table->string('requisition_id');
            $table->string('agreement_id');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bank_institution_id')->references('id')->on('bank_institutions')->cascadeOnDelete();
        });

        Schema::create('user_bank_accounts', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_bank_session_id');
            $table->string('external_id');
            $table->string('resource_id')->nullable();
            $table->string('name')->nullable();
            $table->string('iban')->nullable();
            $table->unsignedBigInteger('balance_cents')->default(0);
            $table->string('currency');
            $table->timestamp('access_expires_at');
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('user_bank_session_id')->references('id')->on('user_bank_sessions')->cascadeOnDelete();
        });

        Schema::create('user_bank_transactions_raw', static function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_bank_account_id');
            $table->string('external_id', 64)->nullable()->index();
            $table->bigInteger('balance_cents')->index();
            $table->string('currency')->index();
            $table->json('currency_exchange')->nullable();
            $table->string('additional_information', 512)->nullable()->index();
            $table->string('remittance_information', 560)->nullable()->index();
            $table->timestamp('booked_at')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->foreign('user_bank_account_id')->references('id')->on('user_bank_accounts')->cascadeOnDelete();
            $table->unique(['user_bank_account_id', 'external_id'], 'user_bank_transaction_unique');
        });

        Schema::create('transaction_tags', static function (Blueprint $table): void {
            $table->id();
            $table->string('tag')->unique()->index();
            $table->string('color')->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });

        Schema::create('user_transaction_tags', static function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->string('tag')->index();
            $blueprint->string('color')->index();
            $blueprint->timestamp('created_at')->useCurrent()->index();
            $blueprint->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $blueprint->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $blueprint->unique(['user_id', 'tag']);
        });

        Schema::create('user_transactions', static function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->unsignedBigInteger('user_id');
            $blueprint->unsignedBigInteger('user_bank_account_id');
            $blueprint->unsignedBigInteger('transaction_tag_id')->nullable();
            $blueprint->unsignedBigInteger('user_transaction_tag_id')->nullable();
            $blueprint->unsignedBigInteger('user_bank_transaction_raw_id');
            $blueprint->bigInteger('balance_cents')->index();
            $blueprint->string('currency', 20)->index();
            $blueprint->string('description', 512)->nullable()->index();
            $blueprint->timestamp('booked_at')->index();
            $blueprint->timestamp('created_at')->useCurrent()->index();
            $blueprint->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $blueprint->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $blueprint->foreign('user_bank_account_id')->references('id')->on('user_bank_accounts')->cascadeOnDelete();
            $blueprint->foreign('transaction_tag_id')->references('id')->on('transaction_tags')->nullOnDelete();
            $blueprint->foreign('user_transaction_tag_id')->references('id')->on('user_transaction_tags')->nullOnDelete();
            $blueprint->foreign('user_bank_transaction_raw_id')->references('id')->on('user_bank_transactions_raw')->cascadeOnDelete();
            $blueprint->unique(['user_id', 'user_bank_transaction_raw_id'], 'user_transaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraken_trading_pairs');
        Schema::dropIfExists('user_kraken_accounts');
        Schema::dropIfExists('user_crypto_wallets');
        Schema::dropIfExists('user_stock_markets');
        Schema::dropIfExists('bank_institutions');
        Schema::dropIfExists('user_manual_entries');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('user_bank_sessions');
        Schema::dropIfExists('user_bank_accounts');
        Schema::dropIfExists('user_bank_transactions_raw');
        Schema::dropIfExists('transaction_tags');
        Schema::dropIfExists('user_transaction_tags');
        Schema::dropIfExists('user_transactions');
    }
};

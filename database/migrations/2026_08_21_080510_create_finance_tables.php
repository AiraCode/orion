<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20)->index();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('subtype')->nullable();
            $table->decimal('balance', 20, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_session_id')->nullable()->constrained()->nullOnDelete();
            $table->date('entry_date');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->decimal('total_amount', 20, 4)->default(0);
            $table->enum('status', ['posted', 'reversed'])->default('posted');
            $table->timestamps();
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 20, 4);
            $table->string('memo')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('game_date');
            $table->string('period_type')->default('daily'); // daily, monthly, quarterly, yearly
            $table->decimal('revenue', 20, 4)->default(0);
            $table->decimal('cogs', 20, 4)->default(0);
            $table->decimal('gross_profit', 20, 4)->default(0);
            $table->decimal('operating_expenses', 20, 4)->default(0);
            $table->decimal('net_profit', 20, 4)->default(0);
            $table->decimal('cash_balance', 20, 4)->default(0);
            $table->decimal('total_assets', 20, 4)->default(0);
            $table->decimal('total_liabilities', 20, 4)->default(0);
            $table->decimal('total_equity', 20, 4)->default(0);
            $table->decimal('valuation', 20, 4)->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'game_date', 'period_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_snapshots');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};

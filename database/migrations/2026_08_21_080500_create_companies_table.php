<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('game_session_id')->nullable()->constrained('game_sessions')->cascadeOnDelete();
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('ticker')->nullable();
            $table->decimal('cash_balance', 20, 4)->default(0);
            $table->string('credit_rating', 5)->default('A');
            $table->decimal('share_price', 20, 4)->default(10.00);
            $table->bigInteger('total_shares')->default(10000000);
            $table->decimal('valuation', 20, 4)->default(0);
            $table->decimal('current_valuation', 20, 4)->default(0);
            $table->decimal('market_share_pct', 5, 2)->default(10.00);
            $table->integer('reputation_score')->default(80);
            $table->date('founded_date')->nullable();
            $table->date('founded_at')->nullable();
            $table->string('currency')->default('USD');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

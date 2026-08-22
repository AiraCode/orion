<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['term_deposit', 'government_bond', 'corporate_bond', 'stock_portfolio', 'real_estate'])->default('term_deposit');
            $table->string('name');
            $table->decimal('principal_amount', 20, 4);
            $table->decimal('current_value', 20, 4);
            $table->decimal('annual_return_rate', 6, 4)->default(0.0500); // 5%
            $table->date('purchase_date');
            $table->date('maturity_date')->nullable();
            $table->enum('status', ['active', 'matured', 'liquidated'])->default('active');
            $table->timestamps();
        });

        Schema::create('shareholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['founder', 'angel', 'vc', 'public', 'employee'])->default('founder');
            $table->bigInteger('share_count')->default(1000000);
            $table->decimal('ownership_percentage', 6, 2)->default(100.00);
            $table->decimal('total_invested', 20, 4)->default(1000000);
            $table->decimal('voting_power_pct', 6, 2)->default(100.00);
            $table->timestamps();
        });

        Schema::create('rd_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('Product Architecture');
            $table->decimal('daily_budget', 20, 4)->default(2500);
            $table->decimal('total_budget', 20, 4)->default(150000);
            $table->decimal('spent_amount', 20, 4)->default(0);
            $table->decimal('progress_pct', 5, 2)->default(0);
            $table->date('start_date');
            $table->date('estimated_completion_date')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'paused'])->default('in_progress');
            $table->json('unlocked_features')->nullable();
            $table->timestamps();
        });

        Schema::create('game_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_session_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['info', 'success', 'warning', 'critical'])->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('category')->default('system'); // finance, operations, hr, market, system
            $table->boolean('is_read')->default(false);
            $table->date('game_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_alerts');
        Schema::dropIfExists('rd_projects');
        Schema::dropIfExists('shareholders');
        Schema::dropIfExists('investments');
    }
};

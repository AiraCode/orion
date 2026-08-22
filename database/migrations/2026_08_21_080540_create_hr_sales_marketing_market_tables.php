<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role_title');
            $table->decimal('salary', 20, 4)->default(5000);
            $table->integer('skill_level')->default(80); // 1-100
            $table->integer('morale')->default(85); // 1-100
            $table->integer('productivity')->default(90); // 1-100
            $table->date('hired_date');
            $table->enum('status', ['active', 'resigned', 'terminated'])->default('active');
            $table->timestamps();
        });

        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('pay_date');
            $table->decimal('total_gross', 20, 4)->default(0);
            $table->decimal('total_tax', 20, 4)->default(0);
            $table->decimal('total_net', 20, 4)->default(0);
            $table->integer('employee_count')->default(0);
            $table->enum('status', ['draft', 'processed', 'cancelled'])->default('processed');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('segment', ['b2b', 'retail', 'enterprise', 'government'])->default('retail');
            $table->string('credit_rating', 5)->default('A');
            $table->integer('loyalty_score')->default(75);
            $table->timestamps();
        });

        Schema::create('sales_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['direct', 'ecommerce', 'retail_distributor', 'wholesale'])->default('ecommerce');
            $table->decimal('commission_rate', 5, 4)->default(0.0500); // 5%
            $table->decimal('monthly_fixed_cost', 20, 4)->default(2000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_channel_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date');
            $table->enum('status', ['pending', 'fulfilled', 'shipped', 'cancelled'])->default('fulfilled');
            $table->decimal('total_amount', 20, 4)->default(0);
            $table->decimal('total_cogs', 20, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 20, 4);
            $table->decimal('unit_cogs', 20, 4)->default(0);
            $table->decimal('total_price', 20, 4);
            $table->timestamps();
        });

        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('channel', ['digital', 'tv', 'print', 'influencer', 'pr'])->default('digital');
            $table->decimal('daily_budget', 20, 4)->default(1000);
            $table->decimal('total_spend', 20, 4)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('reach')->default(0);
            $table->integer('leads_generated')->default(0);
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->timestamps();
        });

        Schema::create('market_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('total_market_size_usd', 20, 4)->default(1000000000);
            $table->decimal('annual_growth_rate', 6, 4)->default(0.0800); // 8%
            $table->integer('price_sensitivity')->default(50); // 0-100
            $table->integer('quality_sensitivity')->default(70); // 0-100
            $table->timestamps();
        });

        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('market_share_pct', 5, 2)->default(15.00);
            $table->integer('brand_rating')->default(80);
            $table->decimal('average_price_index', 5, 2)->default(1.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitors');
        Schema::dropIfExists('market_segments');
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_channels');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('payroll_records');
        Schema::dropIfExists('employees');
    }
};

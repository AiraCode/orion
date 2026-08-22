<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku', 50);
            $table->string('category')->default('Electronics');
            $table->text('description')->nullable();
            $table->decimal('selling_price', 20, 4)->default(0);
            $table->decimal('target_unit_cost', 20, 4)->default(0);
            $table->integer('quality_rating')->default(85);
            $table->integer('appeal_score')->default(80);
            $table->enum('status', ['active', 'discontinued', 'in_development'])->default('active');
            $table->timestamps();

            $table->unique(['company_id', 'sku']);
        });

        Schema::create('product_bills_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_required')->default(1);
            $table->timestamps();

            $table->unique(['product_id', 'raw_material_id']);
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['factory', 'office', 'warehouse', 'lab'])->default('factory');
            $table->enum('status', ['operational', 'under_construction', 'maintenance'])->default('operational');
            $table->integer('building_condition')->default(95); // 0 - 100
            $table->integer('efficiency_rating')->default(90); // 0 - 100
            $table->integer('max_capacity')->default(1000);
            $table->decimal('purchase_cost', 20, 4)->default(0);
            $table->decimal('monthly_operating_cost', 20, 4)->default(25000);
            $table->timestamps();
        });

        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->integer('daily_capacity_units')->default(100);
            $table->decimal('current_utilization_pct', 5, 2)->default(0);
            $table->integer('efficiency_rating')->default(90);
            $table->enum('status', ['running', 'idle', 'maintenance'])->default('idle');
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_ordered');
            $table->integer('quantity_produced')->default(0);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->decimal('total_production_cost', 20, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('production_lines');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('product_bills_of_materials');
        Schema::dropIfExists('products');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 50)->unique();
            $table->string('category')->default('General');
            $table->string('unit', 20)->default('unit');
            $table->decimal('base_cost', 20, 4)->default(0);
            $table->integer('quality_score')->default(80);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('contact_email')->nullable();
            $table->integer('lead_time_days')->default(3);
            $table->integer('quality_rating')->default(85);
            $table->integer('reliability_rating')->default(90);
            $table->decimal('price_multiplier', 8, 4)->default(1.0000);
            $table->timestamps();
        });

        Schema::create('supplier_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 20, 4);
            $table->integer('min_order_quantity')->default(100);
            $table->timestamps();

            $table->unique(['supplier_id', 'raw_material_id']);
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('location')->nullable();
            $table->integer('max_capacity_units')->default(100000);
            $table->integer('current_capacity_units')->default(0);
            $table->decimal('monthly_lease_cost', 20, 4)->default(5000);
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date');
            $table->date('expected_delivery_date');
            $table->date('actual_delivery_date')->nullable();
            $table->enum('status', ['draft', 'ordered', 'in_transit', 'delivered', 'cancelled'])->default('ordered');
            $table->decimal('total_amount', 20, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 20, 4);
            $table->decimal('total_price', 20, 4);
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['raw_material', 'finished_good']);
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->decimal('average_unit_cost', 20, 4)->default(0);
            $table->decimal('total_value', 20, 4)->default(0);
            $table->timestamps();

            $table->unique(['warehouse_id', 'item_type', 'item_id']);
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['raw_material', 'finished_good']);
            $table->unsignedBigInteger('item_id');
            $table->enum('transaction_type', ['purchase_in', 'production_out', 'production_in', 'sales_out', 'adjustment', 'scrap']);
            $table->integer('quantity');
            $table->decimal('unit_cost', 20, 4)->default(0);
            $table->decimal('total_cost', 20, 4)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('supplier_materials');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('raw_materials');
    }
};

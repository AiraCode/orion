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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->nullable()->index();
            $table->string('currency_code', 10)->default('USD');
            $table->string('currency', 10)->default('USD');
            $table->decimal('tax_rate', 5, 4)->default(0);
            $table->decimal('inflation_rate', 5, 4)->default(0);
            $table->decimal('labor_cost_index', 8, 4)->default(1);
            $table->decimal('energy_cost_index', 8, 4)->default(1);
            $table->decimal('logistics_index', 8, 4)->default(1);
            $table->decimal('economic_stability', 5, 4)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

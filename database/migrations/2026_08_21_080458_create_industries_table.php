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
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->nullable()->index();
            $table->text('description')->nullable();
            $table->decimal('market_size', 20, 4)->default(1000000000);
            $table->decimal('base_demand', 20, 4)->default(100);
            $table->decimal('growth_rate', 5, 4)->default(0);
            $table->decimal('volatility', 5, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};

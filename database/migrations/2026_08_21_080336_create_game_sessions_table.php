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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Main Session');
            $table->date('current_game_date')->default('2026-01-01');
            $table->date('game_date')->default('2026-01-01');
            $table->string('speed')->default('1x');
            $table->string('game_speed')->default('1x');
            $table->boolean('is_paused')->default(false);
            $table->string('difficulty')->default('standard');
            $table->string('scenario')->default('standard');
            $table->string('status')->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};

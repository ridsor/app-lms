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
        Schema::create('ukk_answer_theory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukk_result_id')->constrained('ukk_result_theory')->onDelete('cascade');
            $table->morphs('questionable');
            $table->text('answer');
            $table->unique(['ukk_result_id', 'questionable_id', 'questionable_type'], 'unique_answer');
            $table->timestamp('answered_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ukk_answer_theory');
    }
};

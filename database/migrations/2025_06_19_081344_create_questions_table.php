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
        Schema::create('multiple_questions', function (Blueprint $table) {
            $table->id();
            $table->string('questionable_type');
            $table->unsignedBigInteger('questionable_id');
            $table->index(['questionable_type', 'questionable_id'], 'q_idx');
            $table->text('question_text');
            $table->string('option_a_image', 255)->nullable();
            $table->string('option_b_image', 255)->nullable();
            $table->string('option_c_image', 255)->nullable();
            $table->string('option_d_image', 255)->nullable();
            $table->string('option_e_image', 255)->nullable();
            $table->string('option_a', 255);
            $table->string('option_b', 255);
            $table->string('option_c', 255);
            $table->string('option_d', 255)->nullable();
            $table->string('option_e', 255)->nullable();
            $table->string('correct_answer', 255);
            $table->integer('question_points')->nullable();
            $table->string('question_file', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multiple_choice_questions');
    }
};

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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->json('quiz_scores')->nullable();
            $table->json('assignment_scores')->nullable();
            $table->decimal('midterm_score', 5, 1)->nullable();
            $table->decimal('final_score', 5, 1)->nullable();
            $table->decimal('final_grade', 5, 1)->nullable();
            $table->enum('grade_letter', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'schedule_id'], 'unique_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};

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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->enum('type', ['Midterm', 'Final']);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration')->nullable();
            $table->enum('exam_mode', ['Closed Book', 'Open Book'])->default('Closed Book');
            $table->boolean('is_shuffle_questions')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

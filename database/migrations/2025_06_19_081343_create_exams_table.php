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
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->enum('type', ['Quiz', 'Midterm', 'Final', 'Remedial']);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('duration')->nullable();
            $table->enum('exam_mode', ['Closed Book', 'Open Book']);
            $table->enum('display_status', ['Visible', 'Hidden']);
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

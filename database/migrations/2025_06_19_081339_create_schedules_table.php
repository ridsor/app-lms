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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->foreignId('period_id')->constrained('periods');
            $table->string('grouping_schedule');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->enum('meeting_method', ['Online', 'Offline', 'Hybrid'])->default('Offline');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->unique(['class_id', 'day', 'start_time', 'end_time', 'period_id']);
            $table->index('day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

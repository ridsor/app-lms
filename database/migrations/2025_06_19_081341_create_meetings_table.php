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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('schedule_time_id')->references('id')->on('schedule_times')->onDelete('cascade');
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->enum('meeting_method', ['Online', 'Offline', 'Hybrid']);
            $table->enum('type', ['Learning', 'Midterm', 'Final', 'Holiday']);
            $table->dateTime('started_at')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};

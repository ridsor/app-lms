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
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->integer('meeting_number');
            $table->date('date');
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->enum('meeting_method', ['online', 'offline', 'hybrid']);
            $table->enum('type', ['Learning', 'Midterm', 'Final']);
            $table->enum('status', ['not_started', 'started', 'ended'])->default('not_started');
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

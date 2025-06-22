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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->enum('assignment_type', ['individual', 'group'])->default('individual');
            $table->string('file_path', 255)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('deadline_date');
            $table->dateTime('late_submission_date')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

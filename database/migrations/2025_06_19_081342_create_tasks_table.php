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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('meeting_id')->constrained('meetings')->onDelete('cascade');
            $table->enum('type', ['individual', 'group'])->default('individual');
            $table->string('file_path', 255)->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('late_submission_time')->nullable();
            $table->boolean('allow_late_submission')->default(false);
            $table->boolean('value_displayed')->default(false);
            $table->fullText('title');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

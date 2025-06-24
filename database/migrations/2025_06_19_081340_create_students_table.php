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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->unique();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('homeroom_teachers')->onDelete('set null');
            $table->date('date_of_birth');
            $table->string('birthplace', 50);
            $table->enum('gender', ['M', 'F']);
            $table->string('religion', 20);
            $table->year('admission_year');
            $table->enum('status', ['active', 'transferred', 'graduated', 'dropout'])->default('active');
            $table->timestamps();
            $table->fullText('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

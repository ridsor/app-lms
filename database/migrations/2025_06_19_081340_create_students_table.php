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
            $table->foreignId('parent_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('nis', 100)->unique();
            $table->string('nisn', 100)->unique();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->date('date_of_birth');
            $table->string('birthplace', 50);
            $table->enum('gender', ['M', 'F']);
            $table->string('religion', 50);
            $table->year('admission_year');
            $table->enum('status', ['active', 'transferred', 'graduated', 'dropout'])->default('active');
            $table->timestamps();
            $table->fullText('name');
            $table->index('status');
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

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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('nip', 100)->unique();
            $table->string('specialization');
            $table->string('education_level');
            $table->enum('gender', ['M', 'F']);
            $table->date('date_of_birth');
            $table->string('birthplace', 50);
            $table->string('religion', 50);

            $table->timestamps();

            $table->fullText('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

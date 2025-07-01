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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->nullable()->constrained('majors');
            $table->foreignId('homeroom_teacher_id')->nullable()->unique()->constrained('teachers')->onDelete('set null');
            $table->string('name', 20);
            $table->string('level', 20);
            $table->timestamps();

            $table->index('name');
            $table->index('level');
            $table->unique(['level', 'name', 'major_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};

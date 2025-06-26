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
            $table->string('name', 20);
            $table->string('level', 20);
            $table->integer('capacity')->default(30);
            $table->timestamps();

            $table->fullText('name');
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

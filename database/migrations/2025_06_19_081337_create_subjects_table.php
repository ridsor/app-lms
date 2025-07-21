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
        Schema::create('subjects', function (Blueprint $table) {
            $table->string('id', 5)->primary();
            $table->string('curriculum_id');
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->onDelete('cascade');
            $table->string('code', 30)->unique();
            $table->string('name', 100)->unique();
            $table->timestamps();
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

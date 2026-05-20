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
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('gender', ['M', 'F'])->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('birthplace', 50)->nullable()->change();
            $table->string('religion', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('gender', ['M', 'F'])->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('birthplace', 50)->nullable(false)->change();
            $table->string('religion', 50)->nullable(false)->change();
        });
    }
};

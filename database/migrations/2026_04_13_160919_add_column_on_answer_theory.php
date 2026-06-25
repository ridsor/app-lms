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
        Schema::table(
            'ukk_answer_theory',
            function (Blueprint $table) {
                $table->integer('score')->nullable()->after('answer');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'ukk_answer_theory',
            function (Blueprint $table) {
                $table->dropColumn('score');
            }
        );
    }
};

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
            'ukk',
            function (Blueprint $table) {
                $table->foreignId('operator_id')->after('id')->constrained('users')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'ukk',
            function (Blueprint $table) {
                $table->dropForeign(['operator_id']);
                $table->dropColumn('operator_id');
            }
        );
    }
};

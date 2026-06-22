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
        Schema::table('ukk_result_practice', function (Blueprint $table) {
            if (Schema::hasColumn('ukk_result_practice', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn('task_id');
            }
            
            if (!Schema::hasColumn('ukk_result_practice', 'ukk_id')) {
                $table->foreignId('ukk_id')->after('id')->constrained('ukk')->onDelete('cascade');
            }

            if (!Schema::hasColumn('ukk_result_practice', 'score')) {
                $table->decimal('score', 5, 2)->nullable()->after('submitted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ukk_result_practice', function (Blueprint $table) {
            $table->dropForeign(['ukk_id']);
            $table->dropColumn('ukk_id');
            $table->foreignId('task_id')->after('id')->constrained('tasks')->onDelete('cascade');
        });
    }
};

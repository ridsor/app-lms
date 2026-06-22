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
            'exam_answers',
            function (Blueprint $table) {
                // Buat index baru agar foreign key tetap valid saat unique index dihapus
                $table->index('exam_result_id');
                $table->dropUnique('unique_answer');
                
                $table->dropColumn('question_id');
                $table->morphs('questionable');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'exam_answers',
            function (Blueprint $table) {
                $table->unsignedBigInteger('question_id');
                $table->dropMorphs('questionable');
            }
        );
    }
};

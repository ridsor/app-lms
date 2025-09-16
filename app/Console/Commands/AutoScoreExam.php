<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExamResult;
use App\Services\ExamScoringService;
use Illuminate\Support\Facades\Log;

class AutoScoreExam extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'exam:auto-score';

    /**
     * Deskripsi command.
     */
    protected $description = 'Automatic grading for exams that have passed the due date';

    /**
     * Jalankan command.
     */
    public function handle(ExamScoringService $scoringService)
    {
        $examResults = ExamResult::where(function ($q) {
            $q->whereNull('score');
        })
            ->whereNotNull('end_time')
            ->where('end_time', '<', now())
            ->get();

        foreach ($examResults as $examResult) {
            $studentId = $examResult->student_id;

            $scoringService->saveScore($examResult, $studentId);
        }

        $this->info("Completed auto scoring " . count($examResults) . " exam results.");
    }
}

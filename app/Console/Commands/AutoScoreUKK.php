<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UKKResultTheory;
use App\Services\UKKScoringService;

class AutoScoreUKK extends Command
{
    /**
     * Nama dan signature command.
     */
    protected $signature = 'ukk:auto-score';

    /**
     * Deskripsi command.
     */
    protected $description = 'Automatic grading for UKK Teori that have passed the due date';

    /**
     * Jalankan command.
     */
    public function handle(UKKScoringService $scoringService)
    {
        $ukkResults = UKKResultTheory::whereNull('score')
            ->whereHas('ukk', function ($q) {
                $q->whereNotNull('end_time')
                    ->where('end_time', '<', now());
            })
            ->get();

        foreach ($ukkResults as $ukkResult) {
            $studentId = $ukkResult->student_id;

            $scoringService->saveScore($ukkResult, $studentId);
        }

        $this->info("Completed auto scoring " . count($ukkResults) . " UKK Teori results.");
    }
}

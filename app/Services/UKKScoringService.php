<?php

namespace App\Services;

use App\Models\UKKResultTheory;
use App\Models\UKKAnswerTheory;
use App\Models\MultipleQuestion;
use Illuminate\Support\Facades\Log;

class UKKScoringService
{
    /**
     * Hitung nilai dan simpan ke ukk_result_theory.
     */
    public function saveScore(UKKResultTheory $ukkResult, int $studentId): ?UKKResultTheory
    {
        // Ambil jawaban & soal sekaligus
        $answers = UKKAnswerTheory::where('ukk_result_id', $ukkResult->id)
            ->where('questionable_type', MultipleQuestion::class)
            ->get()
            ->keyBy('questionable_id');

        $questions = $ukkResult->ukk->multipleQuestions;

        $totalScore = 0;

        foreach ($questions as $question) {
            $answer = $answers->get($question->id);

            if ($answer) {
                // Tentukan poin: jika benar ambil dari question_points, jika salah 0
                $points = $this->isCorrect($question, $answer)
                    ? (float) $question->question_points
                    : 0;

                // Update kolom score di tabel ukk_answer_theory
                $answer->update([
                    'score' => $points
                ]);

                // Tambahkan ke total skor keseluruhan
                $totalScore += $points;
            }
        }

        // Update tabel ukk_result_theory dengan total skor
        $ukkResult->update([
            'score'  => $totalScore,
            'status' => 'completed',
        ]);

        return $ukkResult;
    }

    /**
     * Cek apakah jawaban benar.
     */
    protected function isCorrect(MultipleQuestion $question, UKKAnswerTheory $answer): bool
    {
        return trim((string) $question->correct_answer) === trim((string) $answer->answer);
    }
}

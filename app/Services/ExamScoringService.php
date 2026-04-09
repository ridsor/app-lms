<?php

namespace App\Services;

use App\Models\ExamResult;
use App\Models\ExamAnswer;
use App\Models\MultipleQuestion;
use Illuminate\Support\Facades\Log;

class ExamScoringService
{
  /**
   * Hitung nilai dan simpan ke exam_results.
   */
  public function saveScore(ExamResult $examResult, int $studentId): ?ExamResult
  {
    // Ambil jawaban & soal sekaligus
    $answers = ExamAnswer::where('exam_result_id', $examResult->id)
      ->where('questionable_type', MultipleQuestion::class)
      ->get()
      ->keyBy('questionable_id');

    $questions = $examResult->exam->multipleQuestions;

    $totalScore = 0;

    // Gunakan foreach agar kita bisa mengupdate setiap baris jawaban
    foreach ($questions as $question) {
      $answer = $answers->get($question->id);

      // Pastikan student menjawab soal tersebut
      if ($answer) {
        // Tentukan poin: jika benar ambil dari question_points, jika salah 0
        $points = $this->isCorrect($question, $answer)
          ? (int) $question->question_points
          : 0;

        // Update kolom score di tabel exam_answers
        $answer->update([
          'score' => $points
        ]);

        // Tambahkan ke total skor keseluruhan
        $totalScore += $points;
      }
    }

    // Update tabel exam_results dengan total skor
    $examResult->update([
      'score'  => $totalScore,
      'status' => 'completed',
    ]);

    return $examResult;
  }

  /**
   * Cek apakah jawaban benar.
   */
  protected function isCorrect(MultipleQuestion $question, ExamAnswer $answer): bool
  {
    return trim((string) $question->correct_answer) === trim((string) $answer->answer);
  }
}

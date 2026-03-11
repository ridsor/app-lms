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
      ->get()
      ->keyBy('question_id'); // supaya lookup lebih cepat

    $questions = MultipleQuestion::where('questionable_id', $examResult->exam_id)->get();

    // Hitung skor
    $score = $questions->sum(function (MultipleQuestion $question) use ($answers) {
      $answer = $answers->get($question->id);
      return ($answer && $this->isCorrect($question, $answer))
        ? (int) $question->question_points
        : 0;
    });

    // Update exam_result
    $examResult->update([
      'score'  => $score,
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

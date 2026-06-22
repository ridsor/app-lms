<?php

namespace App\Http\Middleware;

use App\Models\ExamResult;
use Closure;
use Illuminate\Http\Request;
use App\Services\ExamScoringService;
use Illuminate\Support\Facades\Log;

class CheckExamTime
{
    public function handle(Request $request, Closure $next)
    {
        // Jika student_id tidak sama dengan user_id, mapping di sini
        $student = auth()->user()->student;
        $exam_id = $request->route('id');
        $examResult = ExamResult::where('exam_id', $exam_id)
            ->where('student_id', $student->id)
            ->first();

        if ($examResult && $examResult->exam->end_time && now()->gt($examResult->exam->end_time)) {
            app(ExamScoringService::class)->saveScore($examResult, $student->id);

            return redirect()->route('user.exam.workmanship.result', $exam_id)
                ->with('warning', 'Waktu habis, jawaban otomatis dikirim.');
        }

        return $next($request);
    }
}

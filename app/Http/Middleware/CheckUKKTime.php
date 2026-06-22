<?php

namespace App\Http\Middleware;

use App\Models\UKKResultTheory;
use Closure;
use Illuminate\Http\Request;
use App\Services\UKKScoringService;

class CheckUKKTime
{
    public function handle(Request $request, Closure $next)
    {
        $student = auth()->user()->student;
        $ukk_id = $request->route('id');
        $ukkResult = UKKResultTheory::where('ukk_id', $ukk_id)
            ->where('student_id', $student->id)
            ->first();

        if ($ukkResult && $ukkResult->ukk->end_time && now()->gt($ukkResult->ukk->end_time) && $ukkResult->status !== 'completed') {
            $ukkResult->update(['status' => 'completed']);
            app(UKKScoringService::class)->saveScore($ukkResult, $student->id);

            return redirect()->route('user.ukk.teori.workmanship.result', $ukk_id)
                ->with('error', 'Waktu UKK telah berakhir.');
        }

        return $next($request);
    }
}
